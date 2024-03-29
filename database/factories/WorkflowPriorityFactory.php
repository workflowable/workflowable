<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowPriority;

/**
 * @extends Factory<WorkflowPriority>
 */
class WorkflowPriorityFactory extends Factory
{
    protected $model = WorkflowPriority::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'priority' => $this->faker->numberBetween(1, 10),
        ];
    }
}
