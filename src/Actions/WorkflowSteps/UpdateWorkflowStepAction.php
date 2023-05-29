<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Workflowable\Workflow\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\WorkflowStep;

class UpdateWorkflowStepAction
{
    /**
     * @throws WorkflowStepException
     */
    public function handle(WorkflowStep $workflowStep, array $parameters = [], ?string $name = null, ?string $description = null): WorkflowStep
    {
        /** @var WorkflowStepTypeContract $workflowStepTypeContract */
        $workflowStepTypeContract = app(GetWorkflowStepTypeImplementationAction::class)->handle($workflowStep->workflow_step_type_id, $parameters);

        if (! $workflowStepTypeContract->hasValidParameters()) {
            throw WorkflowStepException::workflowStepTypeParametersInvalid();
        }

        $workflowStep->update([
            'name' => $name ?? $workflowStep->name,
            'description' => $description ?? $workflowStep->description,
            'parameters' => $parameters,
        ]);

        return $workflowStep;
    }
}
