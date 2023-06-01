<?php

namespace Workflowable\Workflow\DataTransferObjects;

class WorkflowTransitionData
{
    public ?string $name = null;
    public ?int $ordinal = null;
    public ?string $ux_uuid = null;
    public ?string $from_workflow_step_ui_uuid = null;
    public ?string $to_workflow_step_ui_uuid = null;

    /**
     * @param array $data array{
     *        name: string,
     *        ordinal: int,
     *        ux_uuid: string,
     *        from_workflow_step_ui_uuid: string,
     *        to_workflow_step_ui_uuid: string,
     *     }
     * @return static
     */
    public static function fromArray(array $data = []): self
    {
        $workflowTransitionData = new WorkflowTransitionData();
        $workflowTransitionData->name = $data['name'];
        $workflowTransitionData->ordinal = $data['ordinal'];
        $workflowTransitionData->ux_uuid = $data['ux_uuid'];
        $workflowTransitionData->from_workflow_step_ui_uuid = $data['from_workflow_step_ui_uuid'];
        $workflowTransitionData->to_workflow_step_ui_uuid = $data['to_workflow_step_ui_uuid'];

        return $workflowTransitionData;
    }
}
