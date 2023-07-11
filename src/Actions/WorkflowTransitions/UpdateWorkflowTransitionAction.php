<?php

namespace Workflowable\Workflowable\Actions\WorkflowTransitions;

use Workflowable\Workflowable\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Exceptions\WorkflowStepException;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowTransition;

class UpdateWorkflowTransitionAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowStepException
     */
    public function handle(WorkflowTransition $workflowTransition, WorkflowTransitionData $workflowTransitionData): WorkflowTransition
    {
        if ($workflowTransition->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        if ($workflowTransitionData->fromWorkflowStep->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
        }

        if ($workflowTransitionData->toWorkflowStep->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
        }

        /** @var WorkflowTransition $workflowTransition */
        $workflowTransition->update([
            'from_workflow_step_id' => $workflowTransitionData->fromWorkflowStep->id,
            'to_workflow_step_id' => $workflowTransitionData->toWorkflowStep->id,
            'name' => $workflowTransitionData->name,
            'ordinal' => $workflowTransitionData->ordinal,
        ]);

        return $workflowTransition;
    }
}
