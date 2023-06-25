<?php

namespace Workflowable\WorkflowEngine\Tests\Fakes;

use Workflowable\WorkflowEngine\Abstracts\AbstractWorkflowEvent;
use Workflowable\WorkflowEngine\Contracts\WorkflowEventContract;
use Workflowable\WorkflowEngine\Traits\PreventOverlappingWorkflowRuns;
use Workflowable\WorkflowEngine\Traits\ValidatesParameters;

class WorkflowEventFake extends AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesParameters;
    use PreventOverlappingWorkflowRuns;

    public function getAlias(): string
    {
        return 'workflow_event_fake';
    }

    public function getName(): string
    {
        return 'Workflow Event Fake';
    }

    public function getRules(): array
    {
        return [
            'test' => 'required|string|min:4',
        ];
    }
}
