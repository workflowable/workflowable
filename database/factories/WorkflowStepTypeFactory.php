<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowStepType;

/**
 * @extends Factory<WorkflowStepType>
 */
class WorkflowStepTypeFactory extends Factory
{
    protected $model = WorkflowStepType::class;

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

    public function withContract(WorkflowStepTypeContract $workflowStepTypeContract): static
    {
        return $this->state(function () use ($workflowStepTypeContract) {
            return [
                'alias' => $workflowStepTypeContract->getAlias(),
                'name' => $workflowStepTypeContract->getName(),
            ];
        })->afterCreating(function (WorkflowStepType $workflowStepType) use ($workflowStepTypeContract) {
            if ($workflowStepTypeContract->getWorkflowEventAliases()) {
                foreach ($workflowStepTypeContract->getWorkflowEventAliases() as $workflowEventAlias) {
                    $workflowEvent = WorkflowEvent::query()->where('alias', $workflowEventAlias)->firstOrFail();
                    $workflowStepType->workflowEvents()->save($workflowEvent);
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
