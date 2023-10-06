<?php

namespace Workflowable\Workflowable\Actions\WorkflowConditions;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflowable\Models\WorkflowCondition;

class UpdateWorkflowConditionAction extends AbstractAction
{
    public function handle(WorkflowCondition $workflowCondition, WorkflowConditionData $workflowConditionData): WorkflowCondition
    {
        $workflowCondition->update([
            'ordinal' => $workflowConditionData->ordinal,
            'parameters' => $workflowConditionData->parameters,
        ]);

        $workflowCondition->workflowConditionParameters()->delete();

        foreach ($workflowConditionData->parameters as $name => $value) {
            $workflowCondition->workflowConditionParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowCondition;
    }
}
