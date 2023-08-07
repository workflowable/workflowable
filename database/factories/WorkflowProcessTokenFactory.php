<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;

/**
 * @extends Factory<WorkflowProcessToken>
 */
class WorkflowProcessTokenFactory extends Factory
{
    protected $model = WorkflowProcessToken::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_process_id' => null,
            'key' => $this->faker->word,
            'value' => $this->faker->word,
            'type' => 'string',
        ];
    }

    public function withWorkflowRun(WorkflowProcess $workflowProcess): static
    {
        return $this->state(function () use ($workflowProcess) {
            return [
                'workflow_process_id' => $workflowProcess->id,
            ];
        });
    }
}
