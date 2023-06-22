<?php

namespace Workflowable\WorkflowEngine\Actions\Workflows;

use Workflowable\WorkflowEngine\Events\Workflows\WorkflowActivated;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;

class ActivateWorkflowAction
{
    /**
     * @throws WorkflowException
     */
    public function handle(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id === WorkflowStatus::ACTIVE) {
            throw WorkflowException::workflowAlreadyActive();
        }

        $workflow->workflow_status_id = WorkflowStatus::ACTIVE;
        $workflow->save();

        WorkflowActivated::dispatch($workflow);

        return $workflow;
    }
}
