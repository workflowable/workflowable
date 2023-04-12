<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Contracts\WorkflowActionContract;
use Workflowable\Workflow\Models\WorkflowAction;
use Workflowable\Workflow\Models\WorkflowActionType;
use Workflowable\Workflow\Tests\Fakes\WorkflowActionFake;

/**
 * @extends Factory<WorkflowAction>
 */
class WorkflowActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workflow_action_type_id' => null,
            'workflow_id' => null,
            'friendly_name' => null,
            'description' => null,
            'parameters' => [],
        ];
    }

    public function withWorkflowActionType(WorkflowActionContract|WorkflowActionType|int|string|null $workflowActionType = null): static
    {
        return $this->state(function () use ($workflowActionType) {
            if (is_string($workflowActionType)) {
                $workflowActionType = WorkflowActionType::query()
                    ->where('alias', $workflowActionType)
                    ->first()
                    ->id;
            } elseif (is_null($workflowActionType)) {
                $workflowActionFake = new WorkflowActionFake();
                $workflowActionType = WorkflowActionType::query()
                    ->where('alias', $workflowActionFake->getAlias())
                    ->firstOr(function () use ($workflowActionFake) {
                        return WorkflowActionType::factory()
                            ->withWorkflowActionContract($workflowActionFake)
                            ->create();
                    })->id;
            } elseif ($workflowActionType instanceof WorkflowActionContract) {
                $workflowActionType = WorkflowActionType::query()
                    ->where('alias', $workflowActionType->getAlias())
                    ->firstOr(function () use ($workflowActionType) {
                        return WorkflowActionType::factory()
                            ->withWorkflowActionContract($workflowActionType)
                            ->create();
                    })->id;
            }

            if ($workflowActionType instanceof WorkflowActionType) {
                $workflowActionType = $workflowActionType->id;
            }

            return ['workflow_action_type_id' => $workflowActionType];
        });
    }
}
