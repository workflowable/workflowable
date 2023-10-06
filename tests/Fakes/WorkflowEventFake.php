<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Concerns\PreventsOverlappingWorkflowProcesses;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;

class WorkflowEventFake extends AbstractWorkflowEvent implements WorkflowEventContract
{
    use PreventsOverlappingWorkflowProcesses;

    public function getRules(): array
    {
        return [
            'test' => 'required|string|min:4',
        ];
    }
}
