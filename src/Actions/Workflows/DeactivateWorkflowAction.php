<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Workflowable\Workflow\Events\Workflows\WorkflowDeactivated;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStatus;

class DeactivateWorkflowAction
{
    /**
     * @throws WorkflowException
     */
    public function handle(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id === WorkflowStatus::INACTIVE) {
            throw WorkflowException::workflowAlreadyInactive();
        }

        $workflow->workflow_status_id = WorkflowStatus::INACTIVE;
        $workflow->save();

        WorkflowDeactivated::dispatch($workflow);

        return $workflow;
    }
}
