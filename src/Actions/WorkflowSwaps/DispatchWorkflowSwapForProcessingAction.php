<?php

namespace Workflowable\Workflowable\Actions\WorkflowSwaps;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Models\WorkflowSwap;

class DispatchWorkflowSwapForProcessingAction extends AbstractAction
{
    public function handle(WorkflowSwap $workflowSwap): WorkflowSwap
    {
        return $workflowSwap;
    }
}
