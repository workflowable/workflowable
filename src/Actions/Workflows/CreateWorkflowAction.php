<?php

namespace Workflowable\Workflowable\Actions\Workflows;

use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Models\WorkflowStatus;

class CreateWorkflowAction
{
    public function handle(string $name, WorkflowEvent|int $workflowEvent, WorkflowPriority|int $workflowPriority, int $retryInterval = 300): Workflow
    {
        /** @var Workflow $workflow */
        $workflow = Workflow::query()->create([
            'name' => $name,
            'workflow_event_id' => $workflowEvent instanceof WorkflowEvent
                ? $workflowEvent->id
                : $workflowEvent,
            'workflow_priority_id' => $workflowPriority instanceof WorkflowPriority
                ? $workflowPriority->id
                : $workflowPriority,
            'workflow_status_id' => WorkflowStatus::DRAFT,
            'retry_interval' => $retryInterval,
        ]);

        return $workflow;
    }
}
