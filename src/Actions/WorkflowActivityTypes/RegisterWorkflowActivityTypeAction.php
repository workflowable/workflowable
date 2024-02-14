<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivityTypes;

use Illuminate\Database\Eloquent\Builder;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowActivityTypeWorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowEvent;

class RegisterWorkflowActivityTypeAction extends AbstractAction
{
    public function handle(WorkflowActivityTypeContract $contract): WorkflowActivityType
    {
        $workflowActivityType = WorkflowActivityType::query()
            ->firstOrCreate([
                'class_name' => $contract::class,
            ], [
                'name' => $contract->getName(),
                'class_name' => $contract::class,
            ]);

        // Purge old records so that we can start with a clean slate with registrations
        if (! $workflowActivityType->wasRecentlyCreated) {
            WorkflowActivityTypeWorkflowEvent::query()
                ->where('workflow_activity_type_id', $workflowActivityType->id)
                ->delete();
        }

        WorkflowEvent::query()
            // When restricted, restrict it to only fetch workflow events that match class names on activity type class
            ->when($contract instanceof ShouldRestrictToWorkflowEvents, function (Builder $query) use ($contract) {
                $query->whereIn('class_name', $contract->getRestrictedWorkflowEventClasses());
            })
            ->eachById(function (WorkflowEvent $workflowEvent) use ($workflowActivityType) {
                WorkflowActivityTypeWorkflowEvent::query()->firstOrCreate([
                    'workflow_activity_type_id' => $workflowActivityType->id,
                    'workflow_event_id' => $workflowEvent->id,
                ], [
                    'workflow_activity_type_id' => $workflowActivityType->id,
                    'workflow_event_id' => $workflowEvent->id,
                ]);
            });

        return $workflowActivityType;
    }
}
