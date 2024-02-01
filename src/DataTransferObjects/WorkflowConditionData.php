<?php

namespace Workflowable\Workflowable\DataTransferObjects;

class WorkflowConditionData
{
    /**
     * @var int This represents the type of condition that will be evaluated.
     */
    public int $workflow_condition_type_id;

    /**
     * @var int This is used to determine the order the conditions are evaluated.
     */
    public int $ordinal;

    /**
     * @var array This is the parameters that will be passed to the condition.
     */
    public array $parameters = [];

    /**
     * @param  array  $data  {
     *                       workflow_condition_type_id: int,
     *                       ordinal: int,
     *                       parameters: array
     *                       }
     */
    public static function fromArray(array $data = []): WorkflowConditionData
    {
        $workflowConditionData = new WorkflowConditionData();
        $workflowConditionData->workflow_condition_type_id = $data['workflow_condition_type_id'];
        $workflowConditionData->ordinal = $data['ordinal'] ?? null;
        $workflowConditionData->parameters = $data['parameters'] ?? [];

        return $workflowConditionData;
    }
}
