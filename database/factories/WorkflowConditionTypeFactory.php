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
            'friendly_name' => $workflowConditionTypeFake->getFriendlyName(),
        ];
    }

    public function withContract(WorkflowConditionTypeContract $workflowConditionTypeContract): static
    {
        return $this->state(function () use ($workflowConditionTypeContract) {
            return [
                'alias' => $workflowConditionTypeContract->getAlias(),
                'friendly_name' => $workflowConditionTypeContract->getFriendlyName(),
            ];
        })->afterCreating(function (WorkflowConditionType $workflowConditionType) use ($workflowConditionTypeContract) {
            if ($workflowConditionTypeContract->getWorkflowEventAlias()) {
                $workflowEvent = WorkflowEvent::query()->where('alias', $workflowConditionTypeContract->getWorkflowEventAlias())->firstOrFail();
                $workflowConditionType->workflowEvent()->save($workflowEvent);
            }
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
