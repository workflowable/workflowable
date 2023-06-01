<?php

namespace Workflowable\Workflow\DataTransferObjects;

class WorkflowStepData
{
    public string $ux_uuid;
    public int $workflow_step_type_id;
    public int $ordinal;
    public string $workflow_ui_uuid;
    public array $parameters = [];

    /**
     * @param array $data{
     *        workflow_step_type_id: int,
     *        description: string,
     *        ux_uuid: string,
     *        name: string,
     *        ordinal: int,
     *        parameters: array
     *     }
     * @return WorkflowStepData
     */
    public static function fromArray(array $data = []): WorkflowStepData
    {
        $workflowStepData = new WorkflowStepData();
        $workflowStepData->ux_uuid = $data['ux_uuid'];
        $workflowStepData->workflow_step_type_id = $data['workflow_step_type_id'];
        $workflowStepData->ordinal = $data['ordinal'];
        $workflowStepData->workflow_ui_uuid = $data['workflow_ui_uuid'];
        $workflowStepData->parameters = $data['parameters'];

        return $workflowStepData;
    }
}
