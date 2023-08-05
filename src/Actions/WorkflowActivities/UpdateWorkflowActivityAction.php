<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivities;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\GetWorkflowActivityTypeImplementationAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowActivityData;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowStatus;

class UpdateWorkflowActivityAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowActivityException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(WorkflowActivity $workflowActivity, WorkflowActivityData $workflowActivityData): WorkflowActivity
    {
        if ($workflowActivity->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
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

        $workflowActivity->update([
            'name' => $workflowActivityData->name ?? $workflowActivity->name,
            'description' => $workflowActivityData->description ?? $workflowActivity->description,
        ]);

        $workflowActivity->workflowActivityParameters()->delete();

        foreach ($workflowActivityData->parameters as $name => $value) {
            $workflowActivity->workflowActivityParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowActivity;
    }
}
