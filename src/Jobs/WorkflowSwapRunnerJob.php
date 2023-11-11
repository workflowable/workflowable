<?php

namespace Workflowable\Workflowable\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Traits\Conditionable;
use Workflowable\Workflowable\Actions\Workflows\ArchiveWorkflowAction;
use Workflowable\Workflowable\Actions\Workflows\ReplaceWorkflowAction;
use Workflowable\Workflowable\Actions\WorkflowSwaps\OutstandingWorkflowProcessSwapAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapCompleted;
use Workflowable\Workflowable\Middleware\CannotSwapWithRunningWorkflowProcesses;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowSwap;

class WorkflowSwapRunnerJob implements ShouldQueue
{
    use Conditionable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WorkflowSwap $workflowSwap)
    {
        //
    }

    public function middleware(): array
    {
        return [
            new CannotSwapWithRunningWorkflowProcesses(),
        ];
    }

    public function handle(): void
    {
        $this->workflowSwap->started_at = now();
        $this->workflowSwap->workflowSwapStatus()->associate(WorkflowSwapStatusEnum::Processing->value);
        $this->workflowSwap->save();

        /**
         * Deactivate the original workflow and ensure that the new workflow will be immediately activated. Using a
         * transaction to prevent a partial change
         */
        ReplaceWorkflowAction::make()->handle($this->workflowSwap->fromWorkflow, $this->workflowSwap->toWorkflow);

        $this->workflowSwap->load('workflowSwapActivityMaps');

        /**
         * Look for outstanding workflow processes that still have workflow activities that can be performed and using
         * the mappings defined on the workflow swap, attempt to convert them over to the new workflow.
         */
        WorkflowProcess::query()
            ->where('workflow_process_status_id', WorkflowProcessStatusEnum::PENDING)
            ->where('workflow_id', $this->workflowSwap->from_workflow_id)
            ->eachById(function (WorkflowProcess $workflowProcess) {
                OutstandingWorkflowProcessSwapAction::make()->handle($this->workflowSwap, $workflowProcess);
            });

        /**
         * Once we have performed every conversion that we are capable of performing, check to see if there is anything
         * outstanding.  If there is not, we can now archive the workflow.
         */
        WorkflowProcess::query()
            ->where('workflow_process_status_id', WorkflowProcessStatusEnum::PENDING)
            ->where('workflow_id', $this->workflowSwap->from_workflow_id)
            ->existsOr(function () {
                ArchiveWorkflowAction::make()->handle($this->workflowSwap->fromWorkflow);
            });

        $this->workflowSwap->completed_at = now();
        $this->workflowSwap->workflowSwapStatus()->associate(WorkflowSwapStatusEnum::Completed->value);
        $this->workflowSwap->save();

        WorkflowSwapCompleted::dispatch($this->workflowSwap);
    }
}
