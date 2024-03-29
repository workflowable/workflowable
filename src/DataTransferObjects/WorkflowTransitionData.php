<?php

namespace Workflowable\Workflowable\DataTransferObjects;

use Illuminate\Support\Collection;
use Workflowable\Workflowable\Models\WorkflowActivity;

class WorkflowTransitionData
{
    public ?int $workflowId = null;

    public ?string $name = null;

    public ?int $ordinal = null;

    public Collection $workflowConditions;

    public WorkflowActivity $fromWorkflowActivity;

    public WorkflowActivity $toWorkflowActivity;

    /**
     * @param  array  $data  array{
     *                       name: string,
     *                       ordinal: int,
     *                       from_workflow_activity: WorkflowActivity,
     *                       to_workflow_activity: WorkflowActivity,
     *                       }
     */
    public static function fromArray(array $data = []): WorkflowTransitionData
    {
        $workflowTransitionData = new WorkflowTransitionData();
        $workflowTransitionData->workflowId = $data['workflow_id'];
        $workflowTransitionData->name = $data['name'];
        $workflowTransitionData->ordinal = $data['ordinal'];
        $workflowTransitionData->fromWorkflowActivity = $data['from_workflow_activity'];
        $workflowTransitionData->toWorkflowActivity = $data['to_workflow_activity'];
        $workflowTransitionData->workflowConditions = collect($data['workflow_conditions'] ?? [])
            ->map(function ($workflowCondition) {
                return WorkflowConditionData::fromArray($workflowCondition);
            });

        return $workflowTransitionData;
    }
}
