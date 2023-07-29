<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowActivityCompletion;

/**
 * @extends Factory<WorkflowActivityCompletion>
 */
class WorkflowActivityCompletionFactory extends Factory
{
    protected $model = WorkflowActivityCompletion::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_activity_id' => null,
            'workflow_run_id' => null,
            'started_at' => $this->faker->dateTime,
            'completed_at' => $this->faker->dateTime,
        ];
    }
}
