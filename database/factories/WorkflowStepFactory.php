<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowStepType;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;

/**
 * @extends Factory<WorkflowStep>
 */
class WorkflowStepFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_step_type_id' => null,
            'workflow_id' => null,
            'friendly_name' => null,
            'description' => null,
            'parameters' => [],
        ];
    }

    public function withWorkflowStepType(WorkflowStepTypeContract|WorkflowStepType|int|string|null $workflowStepType = null): static
    {
        return $this->state(function () use ($workflowStepType) {
            if (is_string($workflowStepType)) {
                $workflowStepType = WorkflowStepType::query()
                    ->where('alias', $workflowStepType)
                    ->first()
                    ->id;
            } elseif (is_null($workflowStepType)) {
                $workflowStepTypeFake = new WorkflowStepTypeFake();
                $workflowStepType = WorkflowStepType::query()
                    ->where('alias', $workflowStepTypeFake->getAlias())
                    ->firstOr(function () use ($workflowStepTypeFake) {
                        return WorkflowStepType::factory()
                            ->withContract($workflowStepTypeFake)
                            ->create();
                    })->id;
            } elseif ($workflowStepType instanceof WorkflowStepTypeContract) {
                $workflowStepType = WorkflowStepType::query()
                    ->where('alias', $workflowStepType->getAlias())
                    ->firstOr(function () use ($workflowStepType) {
                        return WorkflowStepType::factory()
                            ->withContract($workflowStepType)
                            ->create();
                    })->id;
            }

            if ($workflowStepType instanceof WorkflowStepType) {
                $workflowStepType = $workflowStepType->id;
            }

            return ['workflow_step_type_id' => $workflowStepType];
        });
    }
}
