<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowConditions;

use Workflowable\WorkflowEngine\DataTransferObjects\WorkflowConditionData;
use Workflowable\WorkflowEngine\Models\WorkflowCondition;

class UpdateWorkflowConditionAction
{
    public function handle(WorkflowCondition $workflowCondition, WorkflowConditionData $workflowConditionData): WorkflowCondition
    {
        $workflowCondition->update([
            'ordinal' => $workflowConditionData->ordinal,
            'parameters' => $workflowConditionData->parameters,
        ]);

        return $workflowCondition;
    }
}
