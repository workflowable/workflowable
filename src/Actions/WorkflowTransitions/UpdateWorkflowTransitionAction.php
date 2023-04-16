<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Traits\CreatesWorkflowConditions;

class UpdateWorkflowTransitionAction
{
    use CreatesWorkflowConditions;

    /**
     * @param WorkflowTransition $workflowTransition
     * @param string $friendlyName
     * @param int $ordinal
     * @return WorkflowTransition
     * @throws \Workflowable\Workflow\Exceptions\WorkflowConditionException
     */
    public function handle(WorkflowTransition $workflowTransition, string $friendlyName, int $ordinal): WorkflowTransition
    {
        $workflowTransition->update([
            'friendly_name' => $friendlyName,
            'ordinal' => $ordinal,
        ]);

        // Delete all the existing conditions and replace with new ones
        $workflowTransition->workflowConditions()->delete();

        $this->createWorkflowConditions($workflowTransition);

        return $workflowTransition;
    }
}
