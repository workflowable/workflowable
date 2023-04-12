<?php

namespace Workflowable\Workflow\Tests\Fakes;

use Workflowable\Workflow\Contracts\WorkflowEventContract;

class WorkflowEventFake implements WorkflowEventContract
{
    public function __construct(
        public string $test,
    ) {
    }

    public function getAlias(): string
    {
        return 'workflow_event_fake';
    }

    public function getFriendlyName(): string
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
