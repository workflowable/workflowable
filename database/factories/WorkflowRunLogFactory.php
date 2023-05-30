<?php

namespace Workflowable\Workflow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflow\Models\WorkflowRunLog;

/**
 * @extends Factory<WorkflowRunLog>
 */
class WorkflowRunLogFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'loggable_type' => null,
            'loggable_id' => null,
            'level' => null,
            'message' => null,
        ];
    }
}
