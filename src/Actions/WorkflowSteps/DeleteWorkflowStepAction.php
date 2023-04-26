<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;

class DeleteWorkflowStepAction
{
    public function handle(WorkflowStep|int $workflowStep): bool|null
    {
        if (is_int($workflowStep)) {
            $workflowStep = WorkflowStep::query()->findOrFail($workflowStep);
        }

        if ($workflowStep->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        return $workflowStep->delete();
    }
}
