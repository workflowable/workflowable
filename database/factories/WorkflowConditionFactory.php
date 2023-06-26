<?php

namespace Workflowable\WorkflowEngine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\WorkflowEngine\Models\WorkflowCondition;
use Workflowable\WorkflowEngine\Models\WorkflowConditionType;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;

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
            'ordinal' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function withWorkflowTransition(WorkflowTransition|int|null $workflowTransition): self
    {
        return $this->state(fn () => [
            'workflow_transition_id' => $workflowTransition instanceof WorkflowTransition
                ? $workflowTransition->id
                : $workflowTransition,
        ]);
    }

    public function withWorkflowConditionType(WorkflowConditionType|int $workflowConditionType): self
    {
        return $this->state(fn () => [
            'workflow_condition_type_id' => $workflowConditionType instanceof WorkflowConditionType
                ? $workflowConditionType->id
                : $workflowConditionType,
        ]);
    }

    public function withParameters(array $parameters = ['test' => 'test']): static
    {
        return $this->afterCreating(function (WorkflowCondition $workflowCondition) use ($parameters) {
            foreach ($parameters as $name => $value) {
                $workflowCondition->parameters()->create([
                    'key' => $name,
                    'value' => $value,
                ]);
            }
        });
    }
}
