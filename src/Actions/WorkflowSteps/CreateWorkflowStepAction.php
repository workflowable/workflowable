<?php

namespace Workflowable\Workflowable\Actions\WorkflowSteps;

use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowStepData;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Exceptions\WorkflowStepException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowStep;

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
            $workflowStep->workflowConfigurationParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowStep;
    }
}
