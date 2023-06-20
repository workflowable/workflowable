<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeFake;

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
        $workflowConditionTypeFake = new WorkflowConditionTypeFake();

        return [
            'alias' => $workflowConditionTypeFake->getAlias(),
            'name' => $workflowConditionTypeFake->getName(),
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
