<?php

namespace Workflowable\Workflow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\Workflow\Actions\WorkflowRuns\GetNextStepForWorkflowRunAction;
use Workflowable\Workflow\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCompleted;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunFailed;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;

class WorkflowRunnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WorkflowRun $workflowRun)
    {
        //
    }

    /**
     * Implement middleware needed to process the workflow run. This will include:
     *
     * - A middleware that will disallow multiple jobs with the same ID from running at the same time.
     * - Any middleware provided by the workflow event implementation.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowEventException
     */
    public function middleware(): array
    {
        /** @var GetWorkflowEventImplementationAction $getEventImplementation */
        $getEventImplementation = app(GetWorkflowEventImplementationAction::class);

        // Get the workflow run parameters, so that we can hydrate the event implementation
        $workflowRunParameters = $this->workflowRun->workflowRunParameters()
            ->pluck('value', 'key')
            ->toArray();

        // Get the hydrated workflow event implementation
        $workflowEventImplementation = $getEventImplementation->handle($this->workflowRun->workflow->workflow_event_id, $workflowRunParameters);

        // Return all middleware that has been defined as needing to pass before the workflow run can be processed
        return [
            new WithoutOverlapping($this->workflowRun->id),
            ...$workflowEventImplementation->middleware(),
        ];
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
     * Marks the run as complete, so we make no further attempts at processing it.
     */
    public function markRunComplete(): void
    {
        $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::COMPLETED;
        $this->workflowRun->save();

        WorkflowRunCompleted::dispatch($this->workflowRun);
    }

    /**
     * If the workflow run has a next run at date that is in the future, then we should use that date.
     * This is to account for scenarios in which a workflow step has told us explicitly to wait
     * until we hit a certain date or a specific amount of time has passed.
     *
     * By default, we will use the minimum delay between attempts.
     */
    public function scheduleNextRun(): void
    {
        // If we have any workflow transitions remaining, then we need to mark the workflow run as failed
        $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::PENDING;

        $minDelayBetweenAttempts = config('workflowable.delay_between_workflow_run_attempts', 60);
        $this->workflowRun->next_run_at = match (true) {
            $this->workflowRun->next_run_at->isFuture() => $this->workflowRun->next_run_at,

            default => now()->addSeconds($minDelayBetweenAttempts),
        };
        $this->workflowRun->save();
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
            /** @var GetNextStepForWorkflowRunAction $getNextStepAction */
            $getNextStepAction = app(GetNextStepForWorkflowRunAction::class);
            $nextWorkflowStep = $getNextStepAction->handle($this->workflowRun);

            // If an eligible workflow transition was found, then we can proceed to handling the next workflow action
            if ($nextWorkflowStep instanceof WorkflowStep) {
                DB::transaction(function () use ($nextWorkflowStep) {
                    /**
                     * Retrieve the workflow action implementation and execute it
                     *
                     * @var GetWorkflowStepTypeImplementationAction $getWorkflowStepTypeAction
                     */
                    $getWorkflowStepTypeAction = app(GetWorkflowStepTypeImplementationAction::class);
                    $workflowStepTypeContract = $getWorkflowStepTypeAction->handle($nextWorkflowStep->workflow_step_type_id, $nextWorkflowStep->parameters);
                    $workflowStepTypeContract->handle($this->workflowRun, $nextWorkflowStep);

                    // Update the workflow run with the new last workflow action
                    $this->workflowRun->last_workflow_step_id = $nextWorkflowStep->id;
                    $this->workflowRun->save();
                });
            }
        } while ($nextWorkflowStep instanceof WorkflowStep);

        /**
         * If we get here, then we have no more valid workflow transitions to execute as part of this attempt.  Now we
         * need to determine if we have any workflow transitions remaining to execute.  If we do, then we need to mark
         * the workflow run as pending.  If we don't, then we need to mark the workflow run as completed.
         */
        $hasAnyWorkflowTransitionsRemaining = WorkflowTransition::query()
            ->where('from_workflow_step_id', $this->workflowRun->last_workflow_step_id)
            ->exists();

        // If we don't have any workflow transitions remaining, then we need to mark the workflow run as completed
        ! $hasAnyWorkflowTransitionsRemaining
            ? $this->markRunComplete()
            : $this->scheduleNextRun();
    }
}
