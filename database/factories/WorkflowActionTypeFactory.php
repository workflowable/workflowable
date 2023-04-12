<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Workflowable\Fakes\WorkflowActionFake;
use Workflowable\Workflow\Contracts\WorkflowActionContract;
use Workflowable\Workflow\Models\WorkflowActionType;

/**
 * @extends Factory<WorkflowActionType>
 */
class WorkflowActionTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $workflowActionFake = new WorkflowActionFake();

        return [
            'alias' => $workflowActionFake->getAlias(),
            'friendly_name' => $workflowActionFake->getFriendlyName(),
        ];
    }

    public function withWorkflowActionContract(WorkflowActionContract $workflowActionContract): static
    {
        return $this->state(function () use ($workflowActionContract) {
            return [
                'alias' => $workflowActionContract->getAlias(),
                'friendly_name' => $workflowActionContract->getFriendlyName(),
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
