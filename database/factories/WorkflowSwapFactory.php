<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowSwap;

class WorkflowSwapFactory extends Factory
{
    protected $model = WorkflowSwap::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'from_workflow_id' => null,
            'to_workflow_id' => null,
            'workflow_swap_status_id' => null,
            'processed_at' => null,
            'scheduled_at' => null,
        ];
    }

    public function withFromWorkflow(Workflow $fromWorkflow): static
    {
        return $this->state(function () use ($fromWorkflow) {
            return [
                'from_workflow_id' => $fromWorkflow->id,
            ];
        });
    }

    public function withToWorkflow(Workflow $toWorkflow): static
    {
        return $this->state(function () use ($toWorkflow) {
            return [
                'to_workflow_id' => $toWorkflow->id,
            ];
        });
    }

    public function withWorkflowSwapStatus(WorkflowSwapStatusEnum $status): static
    {
        return $this->state(function () use ($status) {
            return [
                'workflow_swap_status_id' => $status->value,
            ];
        });
    }
}
