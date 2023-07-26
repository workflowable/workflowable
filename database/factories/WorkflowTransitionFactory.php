<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowTransition;

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
            'from_workflow_activity_id' => null,
            'to_workflow_activity_id' => null,
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

    public function withFromWorkflowActivity(WorkflowActivity|int $workflowActivity): static
    {
        return $this->state(function () use ($workflowActivity) {
            return [
                'from_workflow_activity_id' => match (true) {
                    $workflowActivity instanceof WorkflowActivity => $workflowActivity->id,
                    is_int($workflowActivity) => $workflowActivity,
                },
            ];
        });
    }

    public function withToWorkflowActivity(WorkflowActivity|int $workflowActivity): static
    {
        return $this->state(function () use ($workflowActivity) {
            return [
                'to_workflow_activity_id' => match (true) {
                    $workflowActivity instanceof WorkflowActivity => $workflowActivity->id,
                    is_int($workflowActivity) => $workflowActivity,
                },
            ];
        });
    }
}
