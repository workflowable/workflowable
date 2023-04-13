<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\WorkflowCondition;

/**
 * @extends Factory<WorkflowCondition>
 */
class WorkflowConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_transition_id' => null,
            'workflow_condition_type_id' => null,
            'ordinal' => null,
            'parameters' => [],
        ];
    }
}
