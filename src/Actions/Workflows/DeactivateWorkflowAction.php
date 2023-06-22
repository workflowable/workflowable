<?php

namespace Workflowable\WorkflowEngine\Actions\Workflows;

use Workflowable\WorkflowEngine\Events\Workflows\WorkflowDeactivated;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;

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
