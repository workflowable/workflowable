<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowSteps;

use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;

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
