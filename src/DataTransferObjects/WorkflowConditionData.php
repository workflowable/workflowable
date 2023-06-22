<?php

namespace Workflowable\WorkflowEngine\DataTransferObjects;

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
     * @var int This is the transition that the condition is attached to.
     */
    public int $workflow_transition_id;

    /**
     * @var array This is the parameters that will be passed to the condition.
     */
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
        $workflowConditionData->workflow_condition_type_id = $data['workflow_condition_type_id'];
        $workflowConditionData->ordinal = $data['ordinal'] ?? null;
        $workflowConditionData->workflow_transition_id = $data['workflow_transition_id'] ?? null;
        $workflowConditionData->parameters = $data['parameters'] ?? [];

        return $workflowConditionData;
    }
}
