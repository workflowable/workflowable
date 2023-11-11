<?php

namespace Workflowable\Workflowable\Actions\WorkflowSwaps;

use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Jobs\WorkflowSwapRunnerJob;
use Workflowable\Workflowable\Models\WorkflowSwap;

class DispatchWorkflowSwapAction extends AbstractAction
{
    public function handle(WorkflowSwap $workflowSwap): WorkflowSwap
    {
        DB::transaction(function () use ($workflowSwap) {
            if (! in_array($workflowSwap->workflow_swap_status_id, [
                WorkflowSwapStatusEnum::Draft,
                WorkflowSwapStatusEnum::Scheduled,
            ])) {
                throw WorkflowSwapException::workflowSwapNotEligibleForDispatch();
            }

            $workflowSwap->workflowSwapStatus()->associate(WorkflowSwapStatusEnum::Dispatched->value);
            $workflowSwap->save();

            WorkflowSwapRunnerJob::dispatch($workflowSwap);
        });

        return $workflowSwap;
    }
}
