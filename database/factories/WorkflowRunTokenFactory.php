<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunToken;

/**
 * @extends Factory<WorkflowRunToken>
 */
class WorkflowRunTokenFactory extends Factory
{
    protected $model = WorkflowRunToken::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_run_id' => null,
            'key' => $this->faker->word,
            'value' => $this->faker->word,
            'type' => 'string',
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
