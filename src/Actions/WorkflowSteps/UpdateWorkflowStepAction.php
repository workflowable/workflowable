<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowSteps;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\WorkflowEngine\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\WorkflowEngine\DataTransferObjects\WorkflowStepData;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Exceptions\WorkflowStepException;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;

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
