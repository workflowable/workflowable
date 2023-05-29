<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStepType;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;

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
        $workflowStepTypeFake = new WorkflowStepTypeFake();

        return [
            'alias' => $workflowStepTypeFake->getAlias(),
            'name' => $workflowStepTypeFake->getName(),
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
            if ($workflowStepTypeContract->getWorkflowEventAlias()) {
                $workflowEvent = WorkflowEvent::query()->where('alias', $workflowStepTypeContract->getWorkflowEventAlias())->firstOrFail();
                $workflowStepType->workflowEvents()->save($workflowEvent);
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
