<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowSteps;

use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\WorkflowEngine\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\WorkflowEngine\DataTransferObjects\WorkflowStepData;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Exceptions\WorkflowStepException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;

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
            'ux_uuid' => $workflowStepData->ux_uuid ?? Str::uuid()->toString(),
        ]);

        // Create the workflow run parameters
        foreach ($workflowStepData->parameters as $name => $value) {
            $workflowStep->parameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowStep;
    }
}
