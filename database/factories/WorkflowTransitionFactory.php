<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;

/**
 * @extends Factory<WorkflowTransition>
 */
class WorkflowTransitionFactory extends Factory
{
    protected $model = WorkflowTransition::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'workflow_id' => null,
            'from_workflow_step_id' => null,
            'to_workflow_step_id' => null,
            'ordinal' => $this->faker->numberBetween(1, 10),
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

    public function withFromWorkflowStep(WorkflowStep|int $workflowStep): static
    {
        return $this->state(function () use ($workflowStep) {
            return [
                'from_workflow_step_id' => match (true) {
                    $workflowStep instanceof WorkflowStep => $workflowStep->id,
                    is_int($workflowStep) => $workflowStep,
                },
            ];
        });
    }

    public function withToWorkflowStep(WorkflowStep|int $workflowStep): static
    {
        return $this->state(function () use ($workflowStep) {
            return [
                'to_workflow_step_id' => match (true) {
                    $workflowStep instanceof WorkflowStep => $workflowStep->id,
                    is_int($workflowStep) => $workflowStep,
                },
            ];
        });
    }
}
