<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowEvents;

use Workflowable\Workflow\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class GetWorkflowEventImplementationActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);
    }

    public function test_can_get_workflow_event_implementation_by_alias(): void
    {
        /** @var GetWorkflowEventImplementationAction $action */
        $action = app(GetWorkflowEventImplementationAction::class);

        $workflowEventContract = $action->handle('workflow_event_fake', [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowEventFake::class, $workflowEventContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowEventContract->getParameters());
    }

    public function test_can_get_workflow_event_implementation_by_id(): void
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        /** @var GetWorkflowEventImplementationAction $action */
        $action = app(GetWorkflowEventImplementationAction::class);

        $workflowEventContract = $action->handle($workflowEvent->id, [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowEventFake::class, $workflowEventContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowEventContract->getParameters());
    }

    public function test_can_get_workflow_event_implementation_by_workflow_event_model(): void
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        /** @var GetWorkflowEventImplementationAction $action */
        $action = app(GetWorkflowEventImplementationAction::class);

        $workflowEventContract = $action->handle($workflowEvent, [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowEventFake::class, $workflowEventContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowEventContract->getParameters());
    }

    public function test_throws_exception_if_workflow_step_type_not_registered(): void
    {
        config()->set('workflowable.workflow_events', []);

        /** @var GetWorkflowEventImplementationAction $action */
        $action = app(GetWorkflowEventImplementationAction::class);

        $this->expectException(WorkflowEventException::class);
        $this->expectExceptionMessage(WorkflowEventException::workflowEventNotRegistered()->getMessage());
        $action->handle('workflow_event_fake', [
            'test' => 'Test',
        ]);
    }
}
