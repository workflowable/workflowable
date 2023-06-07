<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\WorkflowCondition;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowTransition;

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
            'parameters' => [],
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
}
