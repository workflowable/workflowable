<?php

namespace Workflowable\Workflow\DataTransferObjects;

class WorkflowConditionData
{
    public string $ux_uuid;
    public int $workflow_condition_type_id;
    public int $ordinal;
    public string $workflow_transition_ui_uuid;
    public array $parameters = [];

    /**
     * @param array $data {
     *        ux_uuid: string,
     *        workflow_condition_type_id: int,
     *        ordinal: int,
     *        workflow_transition_ui_uuid: string,
     *        parameters: array
     *     }
     * @return WorkflowConditionData
     */
    public static function fromArray(array $data = []): WorkflowConditionData
    {
        $workflowConditionData = new WorkflowConditionData();
        $workflowConditionData->ux_uuid = $data['ux_uuid'];
        $workflowConditionData->workflow_condition_type_id = $data['workflow_condition_type_id'];
        $workflowConditionData->ordinal = $data['ordinal'];
        $workflowConditionData->workflow_transition_ui_uuid = $data['workflow_transition_ui_uuid'];
        $workflowConditionData->parameters = $data['parameters'];

        return $workflowConditionData;
    }
}
