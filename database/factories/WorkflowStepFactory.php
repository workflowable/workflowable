<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowStep;
use Workflowable\Workflowable\Models\WorkflowStepType;

/**
 * @extends Factory<WorkflowStep>
 */
class WorkflowStepFactory extends Factory
{
    protected $model = WorkflowStep::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_step_type_id' => null,
            'workflow_id' => null,
            'name' => $this->faker->name,
            'description' => null,
            'ux_uuid' => $this->faker->uuid,
        ];
    }

    public function withWorkflow(Workflow|int $workflow): static
    {
        return $this->state(function () use ($workflow) {
            return [
                'workflow_id' => match (true) {
                    $workflow instanceof Workflow => $workflow->id,
                    is_int($workflow) => $workflow,
                },
            ];
        });
    }

    public function withWorkflowStepType(WorkflowStepTypeContract|WorkflowStepType|int|string $workflowStepType = null): static
    {
        return $this->state(function () use ($workflowStepType) {
            if (is_string($workflowStepType)) {
                $workflowStepType = WorkflowStepType::query()
                    ->where('alias', $workflowStepType)
                    ->first()
                    ->id;
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

    public function withParameters(array $parameters = ['test' => 'test']): static
    {
        return $this->afterCreating(function (WorkflowStep $workflowStep) use ($parameters) {
            foreach ($parameters as $key => $value) {
                $workflowStep->parameters()->create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        });
    }
}
