<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowProcessActivityLogStatus;

/**
 * @extends Factory<WorkflowProcessActivityLogStatus>
 */
class WorkflowActivityAttemptStatusFactory extends Factory
{
    protected $model = WorkflowProcessActivityLogStatus::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
