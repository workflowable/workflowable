<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;

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
