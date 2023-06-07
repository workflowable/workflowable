<?php

namespace Workflowable\Workflow\DataTransferObjects;

class WorkflowStepData
{
    public string $ux_uuid;

    public int $workflow_step_type_id;

    public ?string $name;

    public ?string $description = null;

    public array $parameters = [];

    /**
     * @param  array  $data{
     *        workflow_step_type_id: int,
     *        description: string,
     *        ux_uuid: string,
     *        name: string,
     *        parameters: array
     *     }
     */
    public static function fromArray(array $data = []): WorkflowStepData
    {
        $workflowStepData = new WorkflowStepData();
        $workflowStepData->ux_uuid = $data['ux_uuid'];
        $workflowStepData->name = $data['name'];
        $workflowStepData->description = $data['description'];
        $workflowStepData->workflow_step_type_id = $data['workflow_step_type_id'];
        $workflowStepData->parameters = $data['parameters'];

        return $workflowStepData;
    }
}
