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
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'friendly_name' => $this->faker->name,
            'alias' => $this->faker->name,
            'description' => $this->faker->text,
        ];
    }

    public function withContract(WorkflowEventContract $workflowEventContract): static
    {
        return $this->state(function () use ($workflowEventContract) {
            return [
                'alias' => $workflowEventContract->getAlias(),
                'friendly_name' => $workflowEventContract->getFriendlyName(),
            ];
        });
    }

    public function withFriendlyName(string $friendlyName): static
    {
        return $this->state(function () use ($friendlyName) {
            return [
                'friendly_name' => $friendlyName,
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
