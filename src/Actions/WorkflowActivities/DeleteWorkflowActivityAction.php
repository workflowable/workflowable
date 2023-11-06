<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivities;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowActivity;

class DeleteWorkflowActivityAction extends AbstractAction
{
    /**
     * @throws WorkflowException
     */
    public function handle(WorkflowActivity|int $workflowActivityToDelete): ?bool
    {
        if (is_int($workflowActivityToDelete)) {
            $workflowActivityToDelete = WorkflowActivity::query()->findOrFail($workflowActivityToDelete);
        }

        if ($workflowActivityToDelete->workflow->workflow_status_id !== WorkflowStatusEnum::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        return $workflowActivityToDelete->delete();
    }
}
