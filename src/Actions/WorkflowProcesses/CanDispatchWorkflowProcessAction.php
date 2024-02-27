<?php

namespace Workflowable\Workflowable\Actions\WorkflowProcesses;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowSwaps\HasWorkflowSwapInProgressAction;
use Workflowable\Workflowable\Models\WorkflowProcess;

class CanDispatchWorkflowProcessAction extends AbstractAction
{
    public function handle(WorkflowProcess $workflowProcess): bool
    {
        return ! HasWorkflowSwapInProgressAction::make()->handle($workflowProcess);
    }
}
