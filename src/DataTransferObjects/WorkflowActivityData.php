<?php

namespace Workflowable\Workflowable\DataTransferObjects;

class WorkflowActivityData
{
    public int $workflow_activity_type_id;

    public ?string $name;

    public ?string $description = null;

    public array $parameters = [];

    /**
     * @param  array  $data{
     *        workflow_activity_type_id: int,
     *        description: string,
     *        name: string,
     *        parameters: array
     *     }
     */
    public static function fromArray(array $data = []): WorkflowActivityData
    {
        $workflowActivityData = new WorkflowActivityData();
        $workflowActivityData->name = $data['name'];
        $workflowActivityData->description = $data['description'];
        $workflowActivityData->workflow_activity_type_id = $data['workflow_activity_type_id'];
        $workflowActivityData->parameters = $data['parameters'];

        return $workflowActivityData;
    }
}
