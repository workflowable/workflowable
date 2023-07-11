<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowEvent;

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
            'alias' => $this->faker->name,
            'name' => $this->faker->name,
        ];
    }

    public function withContract(WorkflowConditionTypeContract $workflowConditionTypeContract): static
    {
        return $this->state(function () use ($workflowConditionTypeContract) {
            return [
                'alias' => $workflowConditionTypeContract->getAlias(),
                'name' => $workflowConditionTypeContract->getName(),
            ];
        })->afterCreating(function (WorkflowConditionType $workflowConditionType) use ($workflowConditionTypeContract) {
            if ($workflowConditionTypeContract->getWorkflowEventAliases()) {
                foreach ($workflowConditionTypeContract->getWorkflowEventAliases() as $workflowEventAlias) {
                    $workflowEvent = WorkflowEvent::query()->where('alias', $workflowEventAlias)->firstOrFail();
                    $workflowConditionType->workflowEvents()->save($workflowEvent);
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
