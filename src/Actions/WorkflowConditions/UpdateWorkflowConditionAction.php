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

        $workflowCondition->parameters()->delete();

        foreach ($workflowConditionData->parameters as $name => $value) {
            $workflowCondition->parameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowCondition;
    }
}
