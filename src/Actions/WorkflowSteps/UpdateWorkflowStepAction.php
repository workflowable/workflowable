<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Workflowable\Workflow\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\WorkflowStatus;
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
     * @throws WorkflowException
     * @throws WorkflowStepException
     */
    public function handle(WorkflowStep $workflowStep, array $parameters = [], ?string $friendlyName = null, ?string $description = null): WorkflowStep
    {
        if ($workflowStep->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        /** @var WorkflowStepTypeContract $workflowStepTypeContract */
        $workflowStepTypeContract = app(GetWorkflowStepTypeImplementationAction::class)->handle($workflowStep->workflow_step_type_id, $parameters);

        if (! $workflowStepTypeContract->hasValidParameters()) {
            throw WorkflowStepException::workflowStepTypeParametersInvalid();
        }

        $workflowStep->update([
            'friendly_name' => $friendlyName ?? $workflowStep->friendly_name,
            'description' => $description ?? $workflowStep->description,
            'parameters' => $parameters,
        ]);

        return $workflowStep;
    }
}
