<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Models\WorkflowEvent;

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
            'description' => $this->faker->text,
            'class_name' => null,
        ];
    }

    public function withContract(WorkflowEventContract $workflowEventContract): static
    {
        return $this->state(function () use ($workflowEventContract) {
            return [
                'name' => $workflowEventContract->getName(),
                'class_name' => $workflowEventContract::class,
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
}
