<?php

namespace Workflowable\Workflow\Actions\WorkflowConditions;

use Workflowable\Workflow\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflow\Models\WorkflowCondition;

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
