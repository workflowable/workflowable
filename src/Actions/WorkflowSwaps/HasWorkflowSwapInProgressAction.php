<?php

namespace Workflowable\Workflowable\Actions\WorkflowSwaps;

use Illuminate\Database\Eloquent\Builder;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowSwap;

class HasWorkflowSwapInProgressAction extends AbstractAction
{
    public function handle(WorkflowProcess $workflowProcess): bool
    {
        return WorkflowSwap::query()
            ->where(function (Builder $query) use ($workflowProcess) {
                $query->where('from_workflow_id', $workflowProcess->workflow_id)
                    ->orWhere('to_workflow_id', $workflowProcess->workflow_id);
            })
            ->whereIn('workflow_swap_status_id', [
                WorkflowSwapStatusEnum::Dispatched,
                WorkflowSwapStatusEnum::Processing,
            ])->exists();
    }
}
