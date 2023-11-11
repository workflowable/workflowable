<?php

namespace Workflowable\Workflowable\Actions\WorkflowSwaps;

use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapScheduled;
use Workflowable\Workflowable\Models\WorkflowSwap;

class ScheduleWorkflowSwapAction extends AbstractAction
{
    public function handle(WorkflowSwap $workflowSwap, Carbon $scheduledAt): WorkflowSwap
    {
        $workflowSwap->workflowSwapStatus()->associate(WorkflowSwapStatusEnum::Scheduled->value);
        $workflowSwap->scheduled_at = $scheduledAt;
        $workflowSwap->save();

        WorkflowSwapScheduled::dispatch($workflowSwap);

        return $workflowSwap;
    }
}
