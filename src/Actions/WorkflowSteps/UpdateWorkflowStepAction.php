<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowStepData;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;

class UpdateWorkflowStepAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowStepException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(WorkflowStep $workflowStep, WorkflowStepData $workflowStepData): WorkflowStep
    {
        if ($workflowStep->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        /** @var GetWorkflowStepTypeImplementationAction $getImplementationAction */
        $getImplementationAction = app(GetWorkflowStepTypeImplementationAction::class);
        $workflowStepTypeContract = $getImplementationAction->handle(
            $workflowStep->workflow_step_type_id,
            $workflowStepData->parameters
        );

        if (! $workflowStepTypeContract->hasValidParameters()) {
            throw WorkflowStepException::workflowStepTypeParametersInvalid();
        }

        $workflowStep->update([
            'name' => $workflowStepData->name ?? $workflowStep->name,
            'description' => $workflowStepData->description ?? $workflowStep->description,
            'parameters' => $workflowStepData->parameters,
        ]);

        return $workflowStep;
    }
}
