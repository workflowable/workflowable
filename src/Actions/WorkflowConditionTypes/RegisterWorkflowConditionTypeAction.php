<?php

namespace Workflowable\Workflowable\Actions\WorkflowConditionTypes;

use Illuminate\Database\Eloquent\Builder;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowConditionTypeWorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowEvent;

class RegisterWorkflowConditionTypeAction extends AbstractAction
{
    public function handle(WorkflowConditionTypeContract $contract): WorkflowConditionType
    {
        $workflowConditionType = WorkflowConditionType::query()
            ->firstOrCreate([
                'class_name' => $contract::class,
            ], [
                'name' => $contract->getName(),
                'class_name' => $contract::class,
            ]);

        // Purge old records so that we can start with a clean slate with registrations
        if (! $workflowConditionType->wasRecentlyCreated) {
            WorkflowConditionTypeWorkflowEvent::query()
                ->where('workflow_condition_type_id', $workflowConditionType->id)
                ->delete();
        }

        WorkflowEvent::query()
            // When restricted, restrict it to only fetch workflow events that match class names on condition type class
            ->when($contract instanceof ShouldRestrictToWorkflowEvents, function (Builder $query) use ($contract) {
                $query->whereIn('class_name', $contract->getRestrictedWorkflowEventClasses());
            })
            ->eachById(function (WorkflowEvent $workflowEvent) use ($workflowConditionType) {
                WorkflowConditionTypeWorkflowEvent::query()->firstOrCreate([
                    'workflow_condition_type_id' => $workflowConditionType->id,
                    'workflow_event_id' => $workflowEvent->id,
                ], [
                    'workflow_condition_type_id' => $workflowConditionType->id,
                    'workflow_event_id' => $workflowEvent->id,
                ]);
            });

        return $workflowConditionType;
    }
}
