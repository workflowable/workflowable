<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowableParameter;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunStatus;

/**
 * @extends Factory<WorkflowableParameter>
 */
class WorkflowRunParameterFactory extends Factory
{
    protected $model = WorkflowableParameter::class;

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
