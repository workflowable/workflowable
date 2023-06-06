<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunParameter;
use Workflowable\Workflow\Models\WorkflowRunStatus;

/**
 * @extends Factory<WorkflowRunParameter>
 */
class WorkflowRunParameterFactory extends Factory
{
    protected $model = WorkflowRunParameter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_run_id' => WorkflowRunStatus::CREATED,
            'name' => $this->faker->word,
            'value' => $this->faker->word,
        ];
    }

    public function withWorkflowRUn(WorkflowRun $workflowRUn): static
    {
        return $this->state(function () use ($workflowRUn) {
            return [
                'workflow_run_id' => $workflowRUn->id,
            ];
        });
    }
}