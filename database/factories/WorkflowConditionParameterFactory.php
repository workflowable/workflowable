<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConditionParameter;

/**
 * @extends Factory<WorkflowConditionParameter>
 */
class WorkflowConditionParameterFactory extends Factory
{
    protected $model = WorkflowConditionParameter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_condition_id' => null,
            'key' => $this->faker->word,
            'value' => $this->faker->word,
        ];
    }

    public function withWorkflowCondition(WorkflowCondition $workflowCondition): static
    {
        return $this->state(function () use ($workflowCondition) {
            return [
                'workflow_condition_id' => $workflowCondition->id,
            ];
        });
    }
}
