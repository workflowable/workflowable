<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowAction;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;

/**
 * @extends Factory<WorkflowRun>
 */
class WorkflowRunFactory extends Factory
{
    protected $model = WorkflowRun::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workflow_id' => null,
            'workflow_run_status_id' => WorkflowRunStatus::CREATED,
            'last_workflow_action_id' => null,
            'first_run_at' => null,
            'last_run_at' => null,
            'next_run_at' => now()->format('Y-m-d H:i:s'),
            'parameters' => [],
        ];
    }

    public function withWorkflow(Workflow $workflow): static
    {
        return $this->state(function () use ($workflow) {
            return [
                'workflow_id' => $workflow->id,
            ];
        });
    }

    public function withWorkflowRunStatus(WorkflowRunStatus|int $workflowRunStatus): static
    {
        return $this->state(function () use ($workflowRunStatus) {
            return [
                'workflow_run_status_id' => match (true) {
                    $workflowRunStatus instanceof WorkflowRunStatus => $workflowRunStatus->id,
                    is_int($workflowRunStatus) => $workflowRunStatus,
                },
            ];
        });
    }

    public function withLastWorkflowAction(WorkflowAction $workflowAction): static
    {
        return $this->state(function () use ($workflowAction) {
            return [
                'last_workflow_action_id' => $workflowAction->id,
            ];
        });
    }
}
