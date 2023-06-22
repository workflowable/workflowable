<?php

namespace Workflowable\WorkflowEngine\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\WorkflowEngine\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\WorkflowEngine\Actions\WorkflowRuns\GetNextStepForWorkflowRunAction;
use Workflowable\WorkflowEngine\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\WorkflowEngine\Events\WorkflowRuns\WorkflowRunCompleted;
use Workflowable\WorkflowEngine\Events\WorkflowRuns\WorkflowRunFailed;
use Workflowable\WorkflowEngine\Exceptions\WorkflowEventException;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowRunStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;

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
        // Return all middleware that has been defined as needing to pass before the workflow run can be processed
        return [
            new WithoutOverlapping($this->workflowRun->id),
            ...$this->getWorkflowEventMiddleware(),
        ];
    }

    /**
     * Execute the job in a deferred fashion
     */
    public function handle(): void
    {
        $this->markAsRunning();

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

    /**
     * Should the workflow run fail, then we need to mark the workflow run as failed.
     */
    public function failed(\Throwable $exception): void
    {
        // If we failed to run the workflow, then we need to mark the workflow run as failed
        $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::FAILED;
        $this->workflowRun->save();

        WorkflowRunFailed::dispatch($this->workflowRun);
    }

    public function markAsRunning(): void
    {
        $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::RUNNING;
        $this->workflowRun->first_run_at ??= now();
        $this->workflowRun->last_run_at = now();
        $this->workflowRun->save();
    }

    /**
     * Marks the run as complete, so we make no further attempts at processing it.
     */
    public function markRunComplete(): void
    {
        $this->workflowRun->workflow_run_status_id = WorkflowRunStatus::COMPLETED;
        $this->workflowRun->completed_at = now();
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

        $this->workflowRun->next_run_at = match (true) {
            $this->workflowRun->next_run_at->isFuture() => $this->workflowRun->next_run_at,
            default => now()->addSeconds($this->workflowRun->workflow->retry_interval),
        };

        $this->workflowRun->save();
    }

    /**
     * For every event we give the option to define middleware that should be processed
     * before the workflow run processing can begin.
     *
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowEventException
     */
    public function getWorkflowEventMiddleware(): array
    {
        /** @var GetWorkflowEventImplementationAction $getEventImplementation */
        $getEventImplementation = app(GetWorkflowEventImplementationAction::class);

        // Get the workflow run parameters, so that we can hydrate the event implementation
        $workflowRunParameters = $this->workflowRun->workflowRunParameters()
            ->pluck('value', 'key')
            ->toArray();

        // Get the hydrated workflow event implementation
        $workflowEventImplementation = $getEventImplementation->handle($this->workflowRun->workflow->workflow_event_id, $workflowRunParameters);

        return $workflowEventImplementation->middleware();
    }
}
