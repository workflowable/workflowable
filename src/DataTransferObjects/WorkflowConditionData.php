<?php

namespace Workflowable\Workflow\DataTransferObjects;

use Workflowable\Workflow\Models\WorkflowTransition;

class WorkflowConditionData
{
    public string $ux_uuid;

    public int $workflow_condition_type_id;

    public int $ordinal;

    public WorkflowTransition $workflowTransition;

    public array $parameters = [];

    /**
     * @param  array  $data {
     *        workflow_condition_type_id: int,
     *        ordinal: int,
     *        workflow_transition: WorkflowTransition,
     *        parameters: array
     *     }
     */
    public static function fromArray(array $data = []): WorkflowConditionData
    {
        $workflowConditionData = new WorkflowConditionData();
        $workflowConditionData->ux_uuid = $data['ux_uuid'];
        $workflowConditionData->workflow_condition_type_id = $data['workflow_condition_type_id'];
        $workflowConditionData->ordinal = $data['ordinal'];
        $workflowConditionData->workflowTransition = $data['workflow_transition'];
        $workflowConditionData->parameters = $data['parameters'];

        return $workflowConditionData;
    }
}
