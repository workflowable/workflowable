<?php

namespace Workflowable\Workflowable\Actions\WorkflowSwaps;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapScheduled;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Models\WorkflowSwap;

class ScheduleWorkflowSwapAction extends AbstractAction
{
    public function handle(WorkflowSwap $workflowSwap, Carbon $scheduledAt): WorkflowSwap
    {
        return DB::transaction(function () use ($workflowSwap, $scheduledAt) {
            if (! in_array($workflowSwap->workflow_swap_status_id, [
                WorkflowSwapStatusEnum::Draft,
                WorkflowSwapStatusEnum::Scheduled,
            ])) {
                throw WorkflowSwapException::workflowSwapNotEligibleForScheduling();
            }

            $workflowSwap->workflowSwapStatus()->associate(WorkflowSwapStatusEnum::Scheduled->value);
            $workflowSwap->scheduled_at = $scheduledAt;
            $workflowSwap->save();

            WorkflowSwapScheduled::dispatch($workflowSwap);
        });
    }
}
