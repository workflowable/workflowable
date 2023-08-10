<?php

namespace Workflowable\Workflowable\Actions\WorkflowTransitions;

use Illuminate\Support\Str;
use Workflowable\Workflowable\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowTransition;

class CreateWorkflowTransitionAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowActivityException
     */
    public function handle(WorkflowTransitionData $workflowTransitionData): WorkflowTransition
    {
        if ($workflowTransitionData->fromWorkflowActivity->workflow->workflow_status_id !== WorkflowStatusEnum::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        if ($workflowTransitionData->fromWorkflowActivity->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow();
        }

        if ($workflowTransitionData->toWorkflowActivity->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow();
        }

        /** @var WorkflowTransition $workflowTransition */
        $workflowTransition = WorkflowTransition::query()->create([
            'workflow_id' => $workflowTransitionData->workflowId,
            'from_workflow_activity_id' => $workflowTransitionData->fromWorkflowActivity->id,
            'to_workflow_activity_id' => $workflowTransitionData->toWorkflowActivity->id,
            'name' => $workflowTransitionData->name,
            'ordinal' => $workflowTransitionData->ordinal,
            'ux_uuid' => $workflowTransitionData->uxUuid ?? Str::uuid()->toString(),
        ]);

        return $workflowTransition;
    }
}
