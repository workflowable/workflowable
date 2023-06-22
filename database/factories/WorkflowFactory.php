<?php

namespace Workflowable\WorkflowEngine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;

/**
 * @extends Factory<Workflow>
 */
class WorkflowFactory extends Factory
{
    protected $model = Workflow::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'workflow_event_id' => null,
            'workflow_status_id' => WorkflowStatus::ACTIVE,
            'retry_interval' => 300,
        ];
    }

    public function withWorkflowEvent(WorkflowEvent $workflowEvent): static
    {
        return $this->state(function () use ($workflowEvent) {
            return [
                'workflow_event_id' => $workflowEvent->id,
            ];
        });
    }

    public function withWorkflowStatus(WorkflowStatus|int $workflowStatus): static
    {
        return $this->state(function () use ($workflowStatus) {
            return [
                'workflow_status_id' => match (true) {
                    $workflowStatus instanceof WorkflowStatus => $workflowStatus->id,
                    is_int($workflowStatus) => $workflowStatus,
                },
            ];
        });
    }
}
