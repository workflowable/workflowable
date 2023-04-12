<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Workflowable\Workflow\Events\Workflows\WorkflowActivated;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStatus;

class ActivateWorkflowAction
{
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
