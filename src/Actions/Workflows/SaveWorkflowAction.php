<?php

namespace Workflowable\Workflowable\Actions\Workflows;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;

class SaveWorkflowAction extends AbstractAction
{
    protected Workflow $workflow;

    public function __construct()
    {
        $this->workflow = new Workflow();
    }

    public function withWorkflow(Workflow $workflow): self
    {
        $this->workflow = $workflow;

        return $this;
    }

    public function handle(string $name, WorkflowEvent|int $workflowEvent, WorkflowPriority|int $workflowPriority, int $retryInterval = 300): Workflow
    {
        $this->workflow->fill([
            'name' => $name,
            'workflow_event_id' => $workflowEvent instanceof WorkflowEvent
                ? $workflowEvent->id
                : $workflowEvent,
            'workflow_priority_id' => $workflowPriority instanceof WorkflowPriority
                ? $workflowPriority->id
                : $workflowPriority,
            'workflow_status_id' => WorkflowStatusEnum::DRAFT,
            'retry_interval' => $retryInterval,
        ])->save();

        return $this->workflow;
    }
}
