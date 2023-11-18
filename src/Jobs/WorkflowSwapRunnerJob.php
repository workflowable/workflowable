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
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapCompleted;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapProcessing;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowSwap;

class WorkflowSwapRunnerJob implements ShouldQueue
{
    use Conditionable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WorkflowSwap $workflowSwap)
    {
        //
    }

    public function handle(): void
    {
        /**
         * Responsible for preventing a workflow swap from taking place while there are existing workflow processes for the from
         * or to workflow on the workflow swap.
         */
        $hasRunningOrDispatchedWorkflowProcesses = WorkflowProcess::query()
            ->running()
            ->whereIn('workflow_id', [
                $this->workflowSwap->from_workflow_id,
                $this->workflowSwap->to_workflow_id,
            ])
            ->exists();

        if ($hasRunningOrDispatchedWorkflowProcesses) {
            $this->release(30);

            return;
        }

        $this->workflowSwap->started_at = now();
        $this->workflowSwap->workflowSwapStatus()->associate(WorkflowSwapStatusEnum::Processing->value);
        $this->workflowSwap->save();

        WorkflowSwapProcessing::dispatch($this->workflowSwap);

        /**
         * Deactivate the original workflow and ensure that the new workflow will be immediately activated. Using a
         * transaction to prevent a partial change
         */
        ReplaceWorkflowAction::make()->handle($this->workflowSwap->fromWorkflow, $this->workflowSwap->toWorkflow);

        $this->workflowSwap->load('workflowSwapActivityMaps');

        /**
         * Look for outstanding workflow processes that still have workflow activities that can be performed and using
         * the mappings defined on the workflow swap, and convert them over to the new workflow.
         */
        WorkflowProcess::query()
            ->active()
            ->where('workflow_id', $this->workflowSwap->from_workflow_id)
            ->eachById(function (WorkflowProcess $workflowProcess) {
                OutstandingWorkflowProcessSwapAction::make()->handle($this->workflowSwap, $workflowProcess);
            });

        /**
         * Double check, to make sure there are no outstanding workflow processes and then archive the workflow
         */
        WorkflowProcess::query()
            ->active()
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
