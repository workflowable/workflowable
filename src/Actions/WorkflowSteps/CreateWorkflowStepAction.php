<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Workflowable\Workflow\Contracts\WorkflowStepTypeManagerContract;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowStepType;

class CreateWorkflowStepAction
{
    /**
     * Create a new step for a workflow.
     *
     *
     *
     * @throws WorkflowStepException
     */
    public function handle(
        Workflow|int $workflow,
        WorkflowStepType|int|string $workflowStepType,
        array $parameters = [],
        ?string $friendlyName = null,
        ?string $description = null
    ): WorkflowStep {
        $workflowStepType = match (true) {
            is_int($workflowStepType) => WorkflowStepType::query()->findOrFail($workflowStepType),
            is_string($workflowStepType) => WorkflowStepType::query()->where('alias', $workflowStepType)->firstOrFail(),
            default => $workflowStepType,
        };

        /** @var WorkflowStepTypeManagerContract $manager */
        $manager = app(WorkflowStepTypeManagerContract::class);

        if (! $manager->isValid($workflowStepType->alias, $parameters)) {
            throw WorkflowStepException::workflowStepTypeParametersInvalid($workflowStepType->alias);
        }

        /** @var WorkflowStep $workflowStep */
        $workflowStep = WorkflowStep::query()->create([
            'workflow_id' => $workflow instanceof Workflow
                ? $workflow->id
                : $workflow,
            'workflow_step_type_id' => $workflowStepType->id,
            'friendly_name' => $friendlyName ?? $workflowStepType->friendly_name,
            'description' => $description,
            'parameters' => $parameters,
        ]);

        return $workflowStep;
    }
}
