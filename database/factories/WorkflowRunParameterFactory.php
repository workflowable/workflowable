<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunParameter;

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
            'workflow_run_id' => null,
            'key' => $this->faker->word,
            'value' => $this->faker->word,
        ];
    }

    public function withWorkflowRun(WorkflowRun $workflowRun): static
    {
        return $this->state(function () use ($workflowRun) {
            return [
                'workflow_run_id' => $workflowRun->id,
            ];
        });
    }
}
