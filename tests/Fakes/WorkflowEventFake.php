<?php

namespace Workflowable\Workflow\Tests\Fakes;

use Workflowable\Workflow\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Traits\ValidatesWorkflowParameters;

class WorkflowEventFake extends AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesWorkflowParameters;

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

    public function middleware(): array
    {
        return [];
    }
}
