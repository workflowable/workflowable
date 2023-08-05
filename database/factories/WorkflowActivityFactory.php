<?php

namespace Workflowable\Workflowable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityType;

/**
 * @extends Factory<WorkflowActivity>
 */
class WorkflowActivityFactory extends Factory
{
    protected $model = WorkflowActivity::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_activity_type_id' => null,
            'workflow_id' => null,
            'name' => $this->faker->name,
            'description' => null,
            'ux_uuid' => $this->faker->uuid,
        ];
    }

    public function withWorkflow(Workflow|int $workflow): static
    {
        return $this->state(function () use ($workflow) {
            return [
                'workflow_id' => match (true) {
                    $workflow instanceof Workflow => $workflow->id,
                    is_int($workflow) => $workflow,
                },
            ];
        });
    }

    public function withWorkflowActivityType(WorkflowActivityTypeContract|WorkflowActivityType|int|string $workflowActivityType = null): static
    {
        return $this->state(function () use ($workflowActivityType) {
            if (is_string($workflowActivityType)) {
                $workflowActivityType = WorkflowActivityType::query()
                    ->where('alias', $workflowActivityType)
                    ->first()
                    ->id;
            } elseif ($workflowActivityType instanceof WorkflowActivityTypeContract) {
                $workflowActivityType = WorkflowActivityType::query()
                    ->where('alias', $workflowActivityType->getAlias())
                    ->firstOr(function () use ($workflowActivityType) {
                        return WorkflowActivityType::factory()
                            ->withContract($workflowActivityType)
                            ->create();
                    })->id;
            }

            if ($workflowActivityType instanceof WorkflowActivityType) {
                $workflowActivityType = $workflowActivityType->id;
            }

            return ['workflow_activity_type_id' => $workflowActivityType];
        });
    }

    public function withParameters(array $parameters = ['test' => 'test']): static
    {
        return $this->afterCreating(function (WorkflowActivity $workflowActivity) use ($parameters) {
            foreach ($parameters as $key => $value) {
                $workflowActivity->workflowActivityParameters()->create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        });
    }
}
