<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessStatus;

/**
 * @extends Factory<WorkflowProcess>
 */
class WorkflowProcessFactory extends Factory
{
    protected $model = WorkflowProcess::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_id' => null,
            'workflow_process_status_id' => WorkflowProcessStatus::CREATED,
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

    public function withWorkflowProcessStatus(WorkflowProcessStatus|int $workflowProcessStatus): static
    {
        return $this->state(function () use ($workflowProcessStatus) {
            return [
                'workflow_process_status_id' => match (true) {
                    $workflowProcessStatus instanceof WorkflowProcessStatus => $workflowProcessStatus->id,
                    is_int($workflowProcessStatus) => $workflowProcessStatus,
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
