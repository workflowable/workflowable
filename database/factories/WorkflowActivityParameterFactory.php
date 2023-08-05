<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityParameter;

/**
 * @extends Factory<WorkflowActivityParameter>
 */
class WorkflowActivityParameterFactory extends Factory
{
    protected $model = WorkflowActivityParameter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_activity_id' => null,
            'key' => $this->faker->word,
            'value' => $this->faker->word,
        ];
    }

    public function withWorkflowActivity(WorkflowActivity $workflowActivity): static
    {
        return $this->state(function () use ($workflowActivity) {
            return [
                'workflow_activity_id' => $workflowActivity->id,
            ];
        });
    }
}
