<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowActivityTypeWorkflowEvent;

class WorkflowActivityTypeWorkflowEventFactory extends Factory
{
    protected $model = WorkflowActivityTypeWorkflowEvent::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_event_id' => null,
            'workflow_activity_type_id' => null,
        ];
    }
}
