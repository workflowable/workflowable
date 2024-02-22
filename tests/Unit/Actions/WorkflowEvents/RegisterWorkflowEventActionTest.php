<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowEvents;

use Workflowable\Workflowable\Actions\WorkflowEvents\RegisterWorkflowEventAction;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class RegisterWorkflowEventActionTest extends TestCase
{
    public function test_registering_a_workflow_event()
    {
        $workflowEvent = RegisterWorkflowEventAction::make()->handle(new WorkflowEventFake());

        $this->assertInstanceOf(WorkflowEvent::class, $workflowEvent);

        $this->assertDatabaseHas(WorkflowEvent::class, [
            'name' => (new WorkflowEventFake())->getName(),
            'class_name' => WorkflowEventFake::class,
        ]);
    }
}
