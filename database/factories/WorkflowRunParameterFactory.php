<?php

namespace Workflowable\WorkflowEngine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\WorkflowEngine\Models\WorkflowEngineParameter;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowRunStatus;

/**
 * @extends Factory<WorkflowEngineParameter>
 */
class WorkflowRunParameterFactory extends Factory
{
    protected $model = WorkflowEngineParameter::class;

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
