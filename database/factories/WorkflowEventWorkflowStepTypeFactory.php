<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\WorkflowEventWorkflowStepType;

class WorkflowEventWorkflowStepTypeFactory extends Factory
{
    protected $model = WorkflowEventWorkflowStepType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_event_id' => null,
            'workflow_step_type_id' => null,
        ];
    }
}