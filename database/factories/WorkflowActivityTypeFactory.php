<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowEvent;

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
            'alias' => $this->faker->name,
            'name' => $this->faker->name,
        ];
    }

    public function withContract(WorkflowActivityTypeContract $workflowActivityTypeContract): static
    {
        return $this->state(function () use ($workflowActivityTypeContract) {
            return [
                'alias' => $workflowActivityTypeContract->getAlias(),
                'name' => $workflowActivityTypeContract->getName(),
            ];
        })->afterCreating(function (WorkflowActivityType $workflowActivityType) use ($workflowActivityTypeContract) {
            if ($workflowActivityTypeContract->getWorkflowEventAliases()) {
                foreach ($workflowActivityTypeContract->getWorkflowEventAliases() as $workflowEventAlias) {
                    $workflowEvent = WorkflowEvent::query()->where('alias', $workflowEventAlias)->firstOrFail();
                    $workflowActivityType->workflowEvents()->save($workflowEvent);
                }
            }
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
