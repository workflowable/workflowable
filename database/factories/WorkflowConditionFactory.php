<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\WorkflowCondition;
use Workflowable\Workflow\Models\WorkflowConditionType;

/**
 * @extends Factory<WorkflowCondition>
 */
class WorkflowConditionFactory extends Factory
{
    protected $model = WorkflowCondition::class;

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
