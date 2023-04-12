<?php

namespace Workflowable\Workflow\Tests\Fakes;

use Workflowable\Workflow\Contracts\WorkflowActionContract;
use Workflowable\Workflow\Models\WorkflowAction;
use Workflowable\Workflow\Models\WorkflowRun;

class WorkflowActionFake implements WorkflowActionContract
{
    public function getFriendlyName(): string
    {
        return 'Workflow Action Fake';
    }

    public function getAlias(): string
    {
        return 'workflow_action_fake';
    }

    public function getRules(): array
    {
        return [
            'test' => 'required',
        ];
    }

    public function getWorkflowEventAlias(): ?string
    {
        return null;
    }

    public function handle(WorkflowRun $workflowRun, WorkflowAction $workflowAction): bool
    {
        return true;
    }
}
