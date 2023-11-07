<?php

namespace Workflowable\Workflowable\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Actions\WorkflowActivities\ExecuteWorkflowActivityAction;
use Workflowable\Workflowable\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\Workflowable\Actions\WorkflowProcesses\GetNextActivityForWorkflowProcessAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCompleted;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessFailed;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowTransition;

class WorkflowProcessRunnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WorkflowProcess $workflowProcess)
    {
        //
    }

    /**
     * Implement middleware needed to process the workflow process. This will include:
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
        // Return all middleware that has been defined as needing to pass before the workflow process can be processed
        $middleware = [
            new WithoutOverlapping($this->workflowProcess->id),
        ];

        $key = $this->getWorkflowProcessLockKey();
        if (! empty($key)) {
            $middleware[] = new WithoutOverlapping($key);
        }

        return $middleware;
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
            $nextWorkflowActivity = GetNextActivityForWorkflowProcessAction::make()->handle($this->workflowProcess);

            // If an eligible workflow transition was found, then we can proceed to handling the next workflow action
            if ($nextWorkflowActivity instanceof WorkflowActivity) {
                ExecuteWorkflowActivityAction::make()->handle($this->workflowProcess, $nextWorkflowActivity);
            }
        } while ($nextWorkflowActivity instanceof WorkflowActivity);

        /**
         * If we get here, then we have no more valid workflow transitions to execute as part of this attempt.  Now we
         * need to determine if we have any workflow transitions remaining to execute.  If we do, then we need to mark
         * the workflow process as pending.  If we don't, then we need to mark the workflow process as completed.
         */
        $hasAnyWorkflowTransitionsRemaining = WorkflowTransition::query()
            ->where('from_workflow_activity_id', $this->workflowProcess->last_workflow_activity_id)
            ->exists();

        // If we don't have any workflow transitions remaining, then we need to mark the workflow process as completed
        ! $hasAnyWorkflowTransitionsRemaining
            ? $this->markProcessComplete()
            : $this->scheduleNextProcessRun();
    }

    /**
     * Should the workflow process fail, then we need to mark the workflow process as failed.
     */
    public function failed(\Throwable $exception): void
    {
        // If we failed to run the workflow, then we need to mark the workflow process as failed
        $this->workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::FAILED;
        $this->workflowProcess->save();

        WorkflowProcessFailed::dispatch($this->workflowProcess);
    }

    public function markAsRunning(): void
    {
        $this->workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::RUNNING;
        $this->workflowProcess->first_run_at ??= now();
        $this->workflowProcess->last_run_at = now();
        $this->workflowProcess->save();
    }

    /**
     * Marks the process as complete, so we make no further attempts at processing it.
     */
    public function markProcessComplete(): void
    {
        $this->workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::COMPLETED;
        $this->workflowProcess->completed_at = now();
        $this->workflowProcess->save();

        WorkflowProcessCompleted::dispatch($this->workflowProcess);
    }

    /**
     * If the workflow process has a next run at date that is in the future, then we should use that date.
     * This is to account for scenarios in which a workflow activity has told us explicitly to wait
     * until we hit a certain date or a specific amount of time has passed.
     *
     * By default, we will use the minimum delay between attempts.
     */
    public function scheduleNextProcessRun(): void
    {
        // If we have any workflow transitions remaining, then we need to mark the workflow process as failed
        $this->workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::PENDING;

        $this->workflowProcess->next_run_at = match (true) {
            $this->workflowProcess->next_run_at->isFuture() => $this->workflowProcess->next_run_at,
            default => now()->addSeconds($this->workflowProcess->workflow->retry_interval),
        };

        $this->workflowProcess->save();
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
    public function getWorkflowProcessLockKey(): ?string
    {
        // Get the workflow run tokens, so that we can hydrate the event implementation
        $workflowProcessTokens = $this->workflowProcess->workflowProcessTokens()
            ->pluck('value', 'key')
            ->toArray();

        // Get the hydrated workflow event implementation
        $workflowEventImplementation = GetWorkflowEventImplementationAction::make()->handle($this->workflowProcess->workflow->workflow_event_id, $workflowProcessTokens);

        if (method_exists($workflowEventImplementation, 'getWorkflowProcessLockKey')) {
            return $workflowEventImplementation->getWorkflowProcessLockKey();
        }

        return null;
    }
}
