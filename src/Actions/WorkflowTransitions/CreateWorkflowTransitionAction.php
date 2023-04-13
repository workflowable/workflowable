<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;

class CreateWorkflowTransitionAction
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

    public function handle(WorkflowStep|int $fromWorkflowAction, WorkflowStep|int $toWorkflowAction): WorkflowTransition
    {
        // Verify the workflow actions belong to the same workflow

        return new WorkflowTransition();
    }
}
