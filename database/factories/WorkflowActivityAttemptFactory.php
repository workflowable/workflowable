<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowActivityAttempt;

/**
 * @extends Factory<WorkflowActivityAttempt>
 */
class WorkflowActivityAttemptFactory extends Factory
{
    protected $model = WorkflowActivityAttempt::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_activity_id' => null,
            'workflow_process_id' => null,
            'workflow_activity_attempt_status_id' => null,
            'started_at' => $this->faker->dateTime,
            'completed_at' => $this->faker->dateTime,
        ];
    }
}
