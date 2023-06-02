<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowStepData;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;

class CreateWorkflowStepAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowStepException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Workflow|int $workflow, WorkflowStepData $workflowStepData): WorkflowStep
    {
        if ($workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        /** @var GetWorkflowStepTypeImplementationAction $getImplementationAction */
        $getImplementationAction = app(GetWorkflowStepTypeImplementationAction::class);
        $workflowStepTypeContract = $getImplementationAction->handle(
            $workflowStepData->workflow_step_type_id,
            $workflowStepData->parameters
        );

        if (! $workflowStepTypeContract->hasValidParameters()) {
            throw WorkflowStepException::workflowStepTypeParametersInvalid();
        }

        /** @var WorkflowStep $workflowStep */
        $workflowStep = WorkflowStep::query()->create([
            'workflow_id' => $workflow instanceof Workflow
                ? $workflow->id
                : $workflow,
            'workflow_step_type_id' => $workflowStepData->workflow_step_type_id,
            'name' => $workflowStepData->name ?? 'N/A',
            'description' => $workflowStepData->description ?? null,
            'parameters' => $workflowStepData->parameters,
            'ux_uuid' => $workflowStepData->ux_uuid ?? Str::uuid()->toString(),
        ]);

        return $workflowStep;
    }
}
