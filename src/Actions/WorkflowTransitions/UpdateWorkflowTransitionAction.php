<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowTransition;

class UpdateWorkflowTransitionAction
{
    protected array $workflowConditions = [];

    public function addWorkflowCondition(WorkflowConditionType|int $workflowConditionType, array $parameters = []): self
    {
        $this->workflowConditions[] = [
            'workflow_condition_type_id' => $workflowConditionType instanceof WorkflowConditionType ? $workflowConditionType->id : $workflowConditionType,
            'parameters' => $parameters,
        ];

        return $this;
    }

    public function handle(WorkflowTransition $workflowTransition): WorkflowTransition
    {
        // Remove all workflow conditions
        // Validate the workflow conditions before allowing them to be created
        // Create the workflow conditions

        return $workflowTransition;
    }
}
