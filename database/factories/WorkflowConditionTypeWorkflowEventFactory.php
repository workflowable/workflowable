<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowConditionTypeWorkflowEvent;

class WorkflowConditionTypeWorkflowEventFactory extends Factory
{
    protected $model = WorkflowConditionTypeWorkflowEvent::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_event_id' => null,
            'workflow_condition_type_id' => null,
        ];
    }
}
