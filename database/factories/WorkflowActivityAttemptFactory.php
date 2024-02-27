<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowProcessActivityLog;

/**
 * @extends Factory<WorkflowProcessActivityLog>
 */
class WorkflowActivityAttemptFactory extends Factory
{
    protected $model = WorkflowProcessActivityLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_activity_id' => null,
            'workflow_process_id' => null,
            'workflow_process_activity_log_status_id' => null,
            'started_at' => $this->faker->dateTime,
            'completed_at' => $this->faker->dateTime,
        ];
    }
}
