<?php

namespace Workflowable\Workflowable\Actions\WorkflowSteps;

use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowStep;

class DeleteWorkflowStepAction
{
    public function handle(WorkflowStep|int $workflowStepToDelete): bool|null
    {
        if (is_int($workflowStepToDelete)) {
            $workflowStepToDelete = WorkflowStep::query()->findOrFail($workflowStepToDelete);
        }

        if ($workflowStepToDelete->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        return $workflowStepToDelete->delete();
    }
}
