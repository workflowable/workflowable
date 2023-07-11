<?php

namespace Workflowable\Workflowable\Actions\WorkflowSteps;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowStepData;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Exceptions\WorkflowStepException;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowStep;

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
        ]);

        $workflowStep->parameters()->delete();

        foreach ($workflowStepData->parameters as $name => $value) {
            $workflowStep->parameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowStep;
    }
}
