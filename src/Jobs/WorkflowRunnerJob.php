<?php

namespace Workflowable\Workflow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Workflowable\Workflow\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflow\Contracts\EvaluateWorkflowTransitionActionContract;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCompleted;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunFailed;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowTransition;

class WorkflowRunnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WorkflowRun $workflowRun)
    {
        //
    }

    /**
     * Disallow multiple jobs with the same ID from running at the same time.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping($this->workflowRun->id)];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // If we failed to run the workflow, then we need to mark the workflow run as failed
        $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::FAILED;
        $this->workflowRun->save();

        WorkflowRunFailed::dispatch($this->workflowRun);
    }

    /**
     * Execute the job in a deferred fashion
     */
    public function handle(): void
    {
        $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::RUNNING;
        $this->workflowRun->first_run_at ??= now();
        $this->workflowRun->last_run_at = now();
        $this->workflowRun->save();

        /**
         * Continue to fetch the first valid workflow transition and execute it until we have
         * no more valid workflow transitions to execute.
         */
        do {

            $workflowTransition = $this->handleGettingFirstValidWorkflowTransition($this->workflowRun);

            // If an eligible workflow transition was found, then we can proceed to handling the next workflow action
            if ($workflowTransition instanceof WorkflowTransition) {
                DB::transaction(function () use ($workflowTransition) {
                    $workflowStep = $workflowTransition->toWorkflowStep;

                    // Retrieve the workflow action implementation and execute it
                    $workflowStepTypeContract = (new GetWorkflowStepTypeImplementationAction())->handle($workflowStep->id, $workflowStep->parameters);
                    $workflowStepTypeContract->handle($this->workflowRun, $workflowTransition->toWorkflowStep);

                    // Update the workflow run with the new last workflow action
                    $this->workflowRun->last_workflow_step_id = $workflowTransition->to_workflow_step_id;
                    $this->workflowRun->save();
                });
            }
        } while ($workflowTransition instanceof WorkflowTransition);

        /**
         * If we get here, then we have no more valid workflow transitions to execute as part of this attempt.  Now we
         * need to determine if we have any workflow transitions remaining to execute.  If we do, then we need to mark
         * the workflow run as pending.  If we don't, then we need to mark the workflow run as completed.
         */
        $hasAnyWorkflowTransitionsRemaining = WorkflowTransition::query()
            ->where('from_workflow_step_id', $this->workflowRun->last_workflow_step_id)
            ->exists();

        // If we don't have any workflow transitions remaining, then we need to mark the workflow run as completed
        if (! $hasAnyWorkflowTransitionsRemaining) {
            $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::COMPLETED;
            $this->workflowRun->save();

            WorkflowRunCompleted::dispatch($this->workflowRun);
        } else {
            // If we have any workflow transitions remaining, then we need to mark the workflow run as failed
            $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::PENDING;

            $minDelayBetweenAttempts = config('workflowable.delay_between_workflow_run_attempts');
            $this->workflowRun->next_run_at = match (true) {
                /**
                 * If the workflow run has a next run at date that is in the future, then we should use that date.
                 * This is to account for scenarios in which a workflow action has told us explicitly to wait
                 * until we hit a certain date or a specific amount of time has passed.
                 */
                ! is_null($this->workflowRun->next_run_at)
                && $this->workflowRun->next_run_at->isFuture() => $this->workflowRun->next_run_at,

                // By default, we will use the minimum delay between attempts
                default => now()->addSeconds($minDelayBetweenAttempts),
            };
            $this->workflowRun->save();
        }
    }

    protected function handleGettingFirstValidWorkflowTransition(WorkflowRun $workflowRun): ?WorkflowTransition
    {
        // Grab all the workflow transitions that start from the last workflow step
        $workflowTransitions = WorkflowTransition::query()
            ->with([
                'workflowConditions.workflowConditionType',
                'toWorkflowStep.workflowStepType',
            ])
            ->where('workflow_id', $workflowRun->workflow_id)
            ->where('from_workflow_step_id', $workflowRun->last_workflow_step_id)
            ->orderBy('ordinal')
            ->get();

        // Iterate through the workflow transitions and see if any of them pass
        foreach ($workflowTransitions as $workflowTransition) {
            $isPassing = app(EvaluateWorkflowTransitionActionContract::class)->handle($workflowTransition);
            if ($isPassing) {
                return $workflowTransition;
            }
        }

        // If we get here, then we have no passing workflow transitions
        return null;
    }
}
