<?php

namespace Workflowable\Workflowable\Middleware;

use Closure;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Jobs\WorkflowSwapRunnerJob;
use Workflowable\Workflowable\Models\WorkflowProcess;

/**
 * Responsible for preventing a workflow swap from taking place while there are existing workflow processes for the from
 * or to workflow on the workflow swap.
 */
class CannotSwapWithRunningWorkflowProcesses
{
    /**
     * Process the queued job.
     *
     * @param  \Closure(object): void  $next
     */
    public function handle(WorkflowSwapRunnerJob $job, Closure $next): void
    {
        WorkflowProcess::query()
            ->whereIn('workflow_id', [
                $job->workflowSwap->from_workflow_id,
                $job->workflowSwap->to_workflow_id,
            ])
            ->whereIn('workflow_process_status_id', [
                WorkflowProcessStatusEnum::DISPATCHED,
                WorkflowProcessStatusEnum::RUNNING,
            ])
            ->doesntExistOr(function () use ($job) {
                $job->release(5);
            });
    }
}
