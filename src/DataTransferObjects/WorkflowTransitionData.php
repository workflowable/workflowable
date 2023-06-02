<?php

namespace Workflowable\Workflow\DataTransferObjects;

use Workflowable\Workflow\Models\WorkflowStep;

class WorkflowTransitionData
{
    public ?int $workflowId = null;
    public ?string $name = null;
    public ?int $ordinal = null;
    public ?string $uxUuid = null;
    public WorkflowStep $fromWorkflowStep;
    public WorkflowStep $toWorkflowStep;

    /**
     * @param array $data array{
     *        name: string,
     *        ordinal: int,
     *        ux_uuid: string,
     *        from_workflow_step: WorkflowStep,
     *        to_workflow_step: WorkflowStep,
     *     }
     * @return WorkflowTransitionData
     */
    public static function fromArray(array $data = []): WorkflowTransitionData
    {
        $workflowTransitionData = new WorkflowTransitionData();
        $workflowTransitionData->workflowId = $data['workflow_id'];
        $workflowTransitionData->name = $data['name'];
        $workflowTransitionData->ordinal = $data['ordinal'];
        $workflowTransitionData->uxUuid = $data['ux_uuid'];
        $workflowTransitionData->fromWorkflowStep = $data['from_workflow_step'];
        $workflowTransitionData->toWorkflowStep = $data['to_workflow_step'];

        return $workflowTransitionData;
    }
}
