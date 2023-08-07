<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Traits\PreventsOverlappingWorkflowProcesses;

class WorkflowEventFake extends AbstractWorkflowEvent implements WorkflowEventContract
{
    use PreventsOverlappingWorkflowProcesses;

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
