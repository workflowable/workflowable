<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Workflowable\Workflow\Models\Workflow;

/**
 * Creates an identical copy of a pre-existing core and all of it's associated data that can then be edited without
 * impacting any existing core runs
 */
class CloneWorkflowAction
{
    public function handle(Workflow $workflow, string $name): Workflow
    {
        $newWorkflow = $workflow->replicate();
        $newWorkflow->name = $name;
        $newWorkflow->save();

        //$this->cloneWorkflowActions($core, $newWorkflow);
        //$this->cloneWorkflowTransitions($core, $newWorkflow);
        //$this->cloneWorkflowConditions($core, $newWorkflow);

        return $newWorkflow;
    }
}
