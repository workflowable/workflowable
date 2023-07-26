<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivities;

use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\GetWorkflowActivityTypeImplementationAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowActivityData;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowActivity;

class CreateWorkflowActivityAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowActivityException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Workflow|int $workflow, WorkflowActivityData $workflowActivityData): WorkflowActivity
    {
        if ($workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        /** @var GetWorkflowActivityTypeImplementationAction $getImplementationAction */
        $getImplementationAction = app(GetWorkflowActivityTypeImplementationAction::class);
        $workflowActivityTypeContract = $getImplementationAction->handle(
            $workflowActivityData->workflow_activity_type_id,
            $workflowActivityData->parameters
        );

        if (! $workflowActivityTypeContract->hasValidParameters()) {
            throw WorkflowActivityException::workflowActivityTypeParametersInvalid();
        }

        /** @var WorkflowActivity $workflowActivity */
        $workflowActivity = WorkflowActivity::query()->create([
            'workflow_id' => $workflow instanceof Workflow
                ? $workflow->id
                : $workflow,
            'workflow_activity_type_id' => $workflowActivityData->workflow_activity_type_id,
            'name' => $workflowActivityData->name ?? 'N/A',
            'description' => $workflowActivityData->description ?? null,
            'ux_uuid' => $workflowActivityData->ux_uuid ?? Str::uuid()->toString(),
        ]);

        // Create the workflow run parameters
        foreach ($workflowActivityData->parameters as $name => $value) {
            $workflowActivity->workflowConfigurationParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowActivity;
    }
}
