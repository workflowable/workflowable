<?php

namespace Workflowable\Workflowable\Actions\Workflows;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\Workflows\WorkflowActivated;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;

class ActivateWorkflowAction extends AbstractAction
{
    public function handle(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id === WorkflowStatusEnum::ACTIVE) {
            throw WorkflowException::workflowAlreadyActive();
        }

        $workflow->workflow_status_id = WorkflowStatusEnum::ACTIVE;
        $workflow->save();

        WorkflowActivated::dispatch($workflow);

        return $workflow;
    }
}
