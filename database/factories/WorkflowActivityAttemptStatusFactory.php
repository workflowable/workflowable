<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowActivityAttemptStatus;

/**
 * @extends Factory<WorkflowActivityAttemptStatus>
 */
class WorkflowActivityAttemptStatusFactory extends Factory
{
    protected $model = WorkflowActivityAttemptStatus::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
