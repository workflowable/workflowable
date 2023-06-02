<?php

namespace Workflowable\Workflow\DataTransferObjects;

use Workflowable\Workflow\Models\Workflow;

class WorkflowData
{
    public Workflow $workflow;

    /**
     * @var WorkflowStepData[]
     */
    public array $workflowSteps = [];

    /**
     * @var WorkflowTransitionData[]
     */
    public array $workflowTransitions = [];

    /**
     * @var WorkflowConditionData[]
     */
    public array $workflowConditions = [];

    /**
     * @param  array  $data{
     *     workflowSteps: array{
     *        workflow_step_type_id: int,
     *        description: string,
     *        ux_uuid: string,
     *        name: string,
     *        ordinal: int,
     *        parameters: array
     *     },
     *     workflowConditions: array{
     *        ux_uuid: string,
     *        workflow_condition_type_id: int,
     *        ordinal: int,
     *        workflow_transition_ui_uuid: string,
     *        parameters: array
     *     },
     *     workflowTransitions: array{
     *        name: string,
     *        ordinal: int,
     *        ux_uuid: string,
     *        from_workflow_step_ui_uuid: string,
     *        to_workflow_step_ui_uuid: string,
     *     }
     * }
     */
    public static function fromArray(Workflow|int $workflow, array $data = []): WorkflowData
    {
        if (is_int($workflow)) {
            $workflow = Workflow::query()->findOrFail($workflow);
        }

        $workflowData = new WorkflowData();
        $workflowData->workflow = $workflow;

        foreach ($data['workflowSteps'] ?? [] as $workflowStepData) {
            WorkflowStepData::fromArray($workflowStepData);
        }

        foreach ($data['workflowTransitions'] ?? [] as $workflowTransitionData) {
            $workflowData->workflowTransitions[] = WorkflowTransitionData::fromArray($workflowTransitionData);
        }

        foreach ($data['workflowConditions'] ?? [] as $workflowConditionData) {
            WorkflowConditionData::fromArray($workflowConditionData);
        }

        return $workflowData;
    }
}
