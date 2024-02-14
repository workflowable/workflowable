<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Models\WorkflowConditionType;

/**
 * @extends Factory<WorkflowConditionType>
 */
class WorkflowConditionTypeFactory extends Factory
{
    protected $model = WorkflowConditionType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'class_name' => null,
        ];
    }

    public function withName(string $name): static
    {
        return $this->state(function () use ($name) {
            return [
                'name' => $name,
            ];
        });
    }

    public function withContract(WorkflowConditionTypeContract $contract): static
    {
        return $this->state(function () use ($contract) {
            return [
                'name' => $contract->getName(),
                'class_name' => $contract::class,
            ];
        });
    }
}
