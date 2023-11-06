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
     * @var WorkflowActivityData[] This is the activities that will be created/updated.
     */
    public array $workflowActivities = [];

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
     *     workflowActivities: array{
     *        workflow_activity_type_id: int,
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
     *        from_workflow_activity_ui_uuid: string,
     *        to_workflow_activity_ui_uuid: string,
     *        workflowConditions: array{
     *          workflow_condition_type_id: int,
     *          ordinal: int,
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

        foreach ($data['workflowActivities'] ?? [] as $workflowActivityData) {
            WorkflowActivityData::fromArray($workflowActivityData);
        }

        foreach ($data['workflowTransitions'] ?? [] as $workflowTransitionData) {
            $workflowData->workflowTransitions[] = WorkflowTransitionData::fromArray($workflowTransitionData);
        }

        return $workflowData;
    }
}
