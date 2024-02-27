<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Models\WorkflowSwapActivityMap;

class WorkflowSwapActivityMapFactory extends Factory
{
    protected $model = WorkflowSwapActivityMap::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_swap_id' => null,
            'from_workflow_activity_id' => null,
            'to_workflow_activity_id' => null,
        ];
    }

    public function withFromWorkflowActivity(WorkflowActivity $fromWorkflowActivity): static
    {
        return $this->state(function () use ($fromWorkflowActivity) {
            return [
                'from_workflow_activity_id' => $fromWorkflowActivity->id,
            ];
        });
    }

    public function withToWorkflowActivity(WorkflowActivity $toWorkflowActivity): static
    {
        return $this->state(function () use ($toWorkflowActivity) {
            return [
                'to_workflow_activity_id' => $toWorkflowActivity->id,
            ];
        });
    }

    public function withWorkflowSwap(WorkflowSwap $workflowSwap): static
    {
        return $this->state(function () use ($workflowSwap) {
            return [
                'workflow_swap_id' => $workflowSwap->id,
            ];
        });
    }
}
