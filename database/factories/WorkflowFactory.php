<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Models\WorkflowStatus;

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
            'workflow_status_id' => WorkflowStatusEnum::ACTIVE,
            'retry_interval' => 300,
            'workflow_priority_id' => WorkflowPriorityFactory::new(),
        ];
    }

    public function withWorkflowPriority(WorkflowPriority $workflowPriority): static
    {
        return $this->state(function () use ($workflowPriority) {
            return [
                'workflow_priority_id' => $workflowPriority->id,
            ];
        });
    }

    public function withWorkflowEvent(WorkflowEvent $workflowEvent): static
    {
        return $this->state(function () use ($workflowEvent) {
            return [
                'workflow_event_id' => $workflowEvent->id,
            ];
        });
    }

    public function withWorkflowStatus(WorkflowStatus|int|WorkflowStatusEnum $workflowStatus): static
    {
        return $this->state(function () use ($workflowStatus) {
            return [
                'workflow_status_id' => match (true) {
                    $workflowStatus instanceof WorkflowStatus => $workflowStatus->id,
                    is_int($workflowStatus) => $workflowStatus,
                    $workflowStatus instanceof WorkflowStatusEnum => $workflowStatus->value,
                },
            ];
        });
    }
}
