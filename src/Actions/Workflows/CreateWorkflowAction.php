<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;

class CreateWorkflowAction
{
    public function handle(string $friendlyName, WorkflowEvent|int $workflowEvent): Workflow
    {
        /** @var Workflow $workflow */
        $workflow = Workflow::query()->create([
            'friendly_name' => $friendlyName,
            'workflow_event_id' => $workflowEvent instanceof WorkflowEvent
                ? $workflowEvent->id
                : $workflowEvent,
            'workflow_status_id' => WorkflowStatus::DRAFT,
        ]);

        return $workflow;
    }
}
