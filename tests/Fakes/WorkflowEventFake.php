<?php

namespace Workflowable\WorkflowEngine\Tests\Fakes;

use Workflowable\WorkflowEngine\Abstracts\AbstractWorkflowEvent;
use Workflowable\WorkflowEngine\Contracts\WorkflowEventContract;
use Workflowable\WorkflowEngine\Traits\PreventsOverlappingWorkflowRuns;
use Workflowable\WorkflowEngine\Traits\ValidatesWorkflowEngineParameters;

class WorkflowEventFake extends AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesWorkflowEngineParameters;
    use PreventsOverlappingWorkflowRuns;

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
