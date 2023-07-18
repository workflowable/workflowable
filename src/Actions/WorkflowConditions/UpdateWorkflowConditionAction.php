<?php

namespace Workflowable\Workflowable\Actions\WorkflowConditions;

use Workflowable\Workflowable\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflowable\Models\WorkflowCondition;

class UpdateWorkflowConditionAction
{
    public function handle(WorkflowCondition $workflowCondition, WorkflowConditionData $workflowConditionData): WorkflowCondition
    {
        $workflowCondition->update([
            'ordinal' => $workflowConditionData->ordinal,
            'parameters' => $workflowConditionData->parameters,
        ]);

        $workflowCondition->workflowConfigurationParameters()->delete();

        foreach ($workflowConditionData->parameters as $name => $value) {
            $workflowCondition->workflowConfigurationParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowCondition;
    }
}
