<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Workflowable\Fakes\WorkflowConditionFake;
use Workflowable\Workflow\Contracts\WorkflowConditionContract;
use Workflowable\Workflow\Models\WorkflowConditionType;

/**
 * @extends Factory<WorkflowConditionType>
 */
class WorkflowConditionTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $workflowActionFake = new WorkflowConditionFake();

        return [
            'alias' => $workflowActionFake->getAlias(),
            'friendly_name' => $workflowActionFake->getFriendlyName(),
        ];
    }

    public function withWorkflowConditionContract(WorkflowConditionContract $workflowConditionContract): static
    {
        return $this->state(function () use ($workflowConditionContract) {
            return [
                'alias' => $workflowConditionContract->getAlias(),
                'friendly_name' => $workflowConditionContract->getFriendlyName(),
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
