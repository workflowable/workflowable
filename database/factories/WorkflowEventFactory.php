<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Models\WorkflowEvent;

/**
 * @extends Factory<WorkflowEvent>
 */
class WorkflowEventFactory extends Factory
{
    protected $model = WorkflowEvent::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'alias' => $this->faker->name,
            'description' => $this->faker->text,
        ];
    }

    public function withContract(WorkflowEventContract $workflowEventContract): static
    {
        return $this->state(function () use ($workflowEventContract) {
            return [
                'alias' => $workflowEventContract->getAlias(),
                'name' => $workflowEventContract->getName(),
            ];
        });
    }

    public function withName(string $name): static
    {
        return $this->state(function () use ($name) {
            return [
                'name' => $name,
            ];
        });
    }

    public function withAlias(string $alias): static
    {
        return $this->state(function () use ($alias) {
            return [
                'alias' => $alias,
            ];
        });
    }
}
