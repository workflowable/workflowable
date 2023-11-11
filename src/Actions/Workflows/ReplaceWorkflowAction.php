<?php

namespace Workflowable\Workflowable\Actions\Workflows;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Models\Workflow;

class ReplaceWorkflowAction extends AbstractAction
{
    public function handle(Workflow $workflowToDeactivate, Workflow $workflowToActivate): Workflow
    {
        /**
         * Todo: As part of the swap process, require a formal swap record be created with mapping functionality.
         *       This  will then be responsible for porting all existing workflow processes into the new workflow.
         *       When doing this, we will also need to be able to freeze all workflow processes in place so that we
         *       can safely perform this swap.
         */

        return $workflowToActivate;
    }
}