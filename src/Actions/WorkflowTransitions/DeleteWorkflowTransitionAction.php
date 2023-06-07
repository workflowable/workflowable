<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowTransition;

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
