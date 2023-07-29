<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivities;

use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowStatus;

class DeleteWorkflowActivityAction
{
    public function handle(WorkflowActivity|int $workflowActivityToDelete): ?bool
    {
        if (is_int($workflowActivityToDelete)) {
            $workflowActivityToDelete = WorkflowActivity::query()->findOrFail($workflowActivityToDelete);
        }

        if ($workflowActivityToDelete->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        return $workflowActivityToDelete->delete();
    }
}
