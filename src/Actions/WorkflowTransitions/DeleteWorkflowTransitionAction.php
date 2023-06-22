<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowTransitions;

use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;

class DeleteWorkflowTransitionAction
{
    /**
     * @throws WorkflowException
     */
    public function handle(WorkflowTransition|int $workflowTransition): void
    {
        if (is_int($workflowTransition)) {
            $workflowTransition = WorkflowTransition::query()->findOrFail($workflowTransition);
        }

        if ($workflowTransition->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        $workflowTransition->delete();
    }
}
