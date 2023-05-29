<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Traits\CreatesWorkflowConditions;

class UpdateWorkflowTransitionAction
{
    use CreatesWorkflowConditions;

    /**
     * @throws \Workflowable\Workflow\Exceptions\WorkflowConditionException
     */
    public function handle(WorkflowTransition $workflowTransition, string $name, int $ordinal): WorkflowTransition
    {
        $workflowTransition->update([
            'name' => $name,
            'ordinal' => $ordinal,
        ]);

        // Delete all the existing conditions and replace with new ones
        $workflowTransition->workflowConditions()->delete();

        $this->createWorkflowConditions($workflowTransition);

        return $workflowTransition;
    }
}
