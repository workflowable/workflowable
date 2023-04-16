<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Workflowable\Workflow\Contracts\WorkflowStepTypeManagerContract;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\WorkflowStep;

class UpdateWorkflowStepAction
{
    /**
     * @param WorkflowStep $workflowStep
     * @param array $parameters
     * @param string|null $friendlyName
     * @param string|null $description
     *
     * @return WorkflowStep
     *
     * @throws WorkflowStepException
     */
    public function handle(WorkflowStep $workflowStep, array $parameters = [], ?string $friendlyName = null, ?string $description = null): WorkflowStep
    {
        /** @var WorkflowStepTypeManagerContract $manager */
        $manager = app(WorkflowStepTypeManagerContract::class);

        if (!$manager->isRegistered($workflowStep->workflowStepType->alias)) {
            throw WorkflowStepException::workflowStepTypeNotRegistered($workflowStep->workflowStepType->alias);
        }

        if (!$manager->isValid($workflowStep->workflowStepType->alias, $parameters)) {
            throw WorkflowStepException::workflowStepTypeParametersInvalid($workflowStep->workflowStepType->alias);
        }

        $workflowStep->update([
            'friendly_name' => $friendlyName ?? $workflowStep->friendly_name,
            'description' => $description ?? $workflowStep->description,
            'parameters' => $parameters,
        ]);

        return $workflowStep;
    }
}
