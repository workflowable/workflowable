<?php

namespace Workflowable\Workflowable\Actions\WorkflowTransitions;

use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowTransition;

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
