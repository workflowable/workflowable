<?php

namespace Workflowable\Workflowable\Actions\WorkflowTransitions;

use Workflowable\Workflowable\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowTransition;

class UpdateWorkflowTransitionAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowActivityException
     */
    public function handle(WorkflowTransition $workflowTransition, WorkflowTransitionData $workflowTransitionData): WorkflowTransition
    {
        if ($workflowTransition->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        if ($workflowTransitionData->fromWorkflowActivity->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow();
        }

        if ($workflowTransitionData->toWorkflowActivity->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow();
        }

        /** @var WorkflowTransition $workflowTransition */
        $workflowTransition->update([
            'from_workflow_activity_id' => $workflowTransitionData->fromWorkflowActivity->id,
            'to_workflow_activity_id' => $workflowTransitionData->toWorkflowActivity->id,
            'name' => $workflowTransitionData->name,
            'ordinal' => $workflowTransitionData->ordinal,
        ]);

        return $workflowTransition;
    }
}
