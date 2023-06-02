<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Workflowable\Workflow\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowTransition;

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
        $workflowTransition = WorkflowTransition::query()->create([
            'from_workflow_step_id' => $workflowTransitionData->fromWorkflowStep->id,
            'to_workflow_step_id' => $workflowTransitionData->toWorkflowStep->id,
            'name' => $workflowTransitionData->name,
            'ordinal' => $workflowTransitionData->ordinal,
        ]);

        return $workflowTransition;
    }
}
