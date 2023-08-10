<?php

namespace Workflowable\Workflowable\Actions\WorkflowTransitions;

use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowException;
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

        if ($workflowTransition->workflow->workflow_status_id !== WorkflowStatusEnum::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        $workflowTransition->delete();
    }
}
