<?php

namespace Workflowable\Workflowable\DataTransferObjects;

use Workflowable\Workflowable\Models\Workflow;

class WorkflowData
{
    /**
     * @var Workflow This is the workflow that will be created/updated.
     */
    public Workflow $workflow;

    /**
     * @var WorkflowStepData[] This is the steps that will be created/updated.
     */
    public array $workflowSteps = [];

    /**
     * @var WorkflowTransitionData[] This is the transitions that will be created/updated.
     */
    public array $workflowTransitions = [];

    /**
     * @var WorkflowConditionData[] This is the conditions that will be created/updated.
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
     *     workflowTransitions: array{
     *        name: string,
     *        ordinal: int,
     *        ux_uuid: string,
     *        from_workflow_step_ui_uuid: string,
     *        to_workflow_step_ui_uuid: string,
     *        workflowConditions: array{
     *          workflow_condition_type_id: int,
     *          ordinal: int,
     *          workflow_transition_ui_uuid: string,
     *          parameters: array
     *        }
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

        return $workflowData;
    }
}
