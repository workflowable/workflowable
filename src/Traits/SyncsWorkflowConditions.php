<?php

namespace Workflowable\Workflow\Traits;

use Illuminate\Support\Collection;
use Workflowable\Workflow\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflow\Models\WorkflowCondition;

trait SyncsWorkflowConditions
{
    /**
     * Takes a collection of WorkflowConditionData and syncs them to the database, while removing any existing workflow
     * conditions touching the same workflow transitions.
     *
     * @param  Collection<WorkflowConditionData>  $workflowConditionDataCollection
     */
    public function syncWorkflowConditions(Collection $workflowConditionDataCollection): int
    {
        $workflowTransitionIds = $workflowConditionDataCollection->pluck('workflow_transition_id')->unique()->toArray();

        WorkflowCondition::query()
            ->whereIn('workflow_transition_id', $workflowTransitionIds)
            ->delete();

        $upsertData = $workflowConditionDataCollection->map(function ($workflowConditionData) {
            return [
                'workflow_transition_id' => $workflowConditionData->workflow_transition_id,
                'workflow_condition_type_id' => $workflowConditionData->workflow_condition_type_id,
                'ordinal' => $workflowConditionData->ordinal,
                'parameters' => $workflowConditionData->parameters,
            ];
        });

        return WorkflowCondition::query()
            ->upsert($upsertData->toArray(), ['workflow_transition_id', 'workflow_condition_type_id'], ['ordinal', 'parameters']);
    }
}
