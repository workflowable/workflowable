<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivities;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\GetWorkflowActivityTypeImplementationAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowActivityData;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowActivity;

class UpdateWorkflowActivityAction extends AbstractAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowActivityException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(WorkflowActivity $workflowActivity, WorkflowActivityData $workflowActivityData): WorkflowActivity
    {
        if ($workflowActivity->workflow->workflow_status_id !== WorkflowStatusEnum::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        $workflowActivityTypeContract = GetWorkflowActivityTypeImplementationAction::make()->handle(
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
