<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunStatus;
use Workflowable\Workflowable\Models\WorkflowActivity;

/**
 * @extends Factory<WorkflowRun>
 */
class WorkflowRunFactory extends Factory
{
    protected $model = WorkflowRun::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_id' => null,
            'workflow_run_status_id' => WorkflowRunStatus::CREATED,
            'last_workflow_activity_id' => null,
            'first_run_at' => null,
            'last_run_at' => null,
            'next_run_at' => now()->format('Y-m-d H:i:s'),
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

    public function withLastWorkflowActivity(WorkflowActivity $workflowActivity): static
    {
        return $this->state(function () use ($workflowActivity) {
            return [
                'last_workflow_activity_id' => $workflowActivity->id,
            ];
        });
    }
}
