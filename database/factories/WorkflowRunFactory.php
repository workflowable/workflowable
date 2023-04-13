<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowStep;

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
            'last_workflow_step_id' => null,
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

    public function withLastWorkflowStep(WorkflowStep $workflowStep): static
    {
        return $this->state(function () use ($workflowStep) {
            return [
                'last_workflow_step_id' => $workflowStep->id,
            ];
        });
    }
}
