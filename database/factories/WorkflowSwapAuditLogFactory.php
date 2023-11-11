<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Models\WorkflowSwapAuditLog;

class WorkflowSwapAuditLogFactory extends Factory
{
    protected $model = WorkflowSwapAuditLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_swap_id' => null,
            'from_workflow_process_id' => null,
            'from_workflow_process_activity_id' => null,
            'to_workflow_process_id' => null,
            'to_workflow_process_activity_id' => null,
        ];
    }

    public function withFromWorkflowProcess(WorkflowProcess $fromWorkflowProcess): static
    {
        return $this->state(function () use ($fromWorkflowProcess) {
            return [
                'from_workflow_process_id' => $fromWorkflowProcess->id,
            ];
        });
    }

    public function withToWorkflowProcess(WorkflowProcess $toWorkflowProcess): static
    {
        return $this->state(function () use ($toWorkflowProcess) {
            return [
                'to_workflow_process_id' => $toWorkflowProcess->id,
            ];
        });
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
