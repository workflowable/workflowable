<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Models\WorkflowActivityType;

/**
 * @extends Factory<WorkflowActivityType>
 */
class WorkflowActivityTypeFactory extends Factory
{
    protected $model = WorkflowActivityType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'class_name' => null,
            'name' => $this->faker->name,
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

    public function withContract(WorkflowActivityTypeContract $contract): static
    {
        return $this->state(function () use ($contract) {
            return [
                'name' => $contract->getName(),
                'class_name' => $contract::class,
            ];
        });
    }
}
