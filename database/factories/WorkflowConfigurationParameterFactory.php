<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConfigurationParameter;
use Workflowable\Workflowable\Models\WorkflowStep;

/**
 * @extends Factory<WorkflowConfigurationParameter>
 */
class WorkflowConfigurationParameterFactory extends Factory
{
    protected $model = WorkflowConfigurationParameter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'parameterizable_id' => null,
            'parameterizable_type' => null,
            'key' => $this->faker->word,
            'value' => $this->faker->word,
            'type' => 'string',
        ];
    }

    public function withWorkflowCondition(WorkflowCondition $workflowCondition): static
    {
        return $this->state(function () use ($workflowCondition) {
            return [
                'parameterizable_id' => $workflowCondition->id,
                'parameterizable_type' => WorkflowCondition::class,
            ];
        });
    }

    public function withWorkflowStep(WorkflowStep $workflowStep): static
    {
        return $this->state(function () use ($workflowStep) {
            return [
                'parameterizable_id' => $workflowStep->id,
                'parameterizable_type' => WorkflowStep::class,
            ];
        });
    }
}
