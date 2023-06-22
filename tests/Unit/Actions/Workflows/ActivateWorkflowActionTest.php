<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\Workflows;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Workflowable\WorkflowEngine\Actions\Workflows\ActivateWorkflowAction;
use Workflowable\WorkflowEngine\Events\Workflows\WorkflowActivated;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\TestCase;

class ActivateWorkflowActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that an inactive workflow can be activated successfully
     */
    public function test_can_activate_inactive_workflow(): void
    {
        Event::fake();
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::INACTIVE)
            ->create();

        $action = new ActivateWorkflowAction();

        $result = $action->handle($workflow);

        $this->assertEquals(WorkflowStatus::ACTIVE, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatus::ACTIVE,
        ]);

        Event::assertDispatched(WorkflowActivated::class, function ($event) use ($result) {
            return $event->workflow->id === $result->id;
        });
    }

    /**
     * Test that an active workflow cannot be activated again
     */
    public function test_cannot_activate_already_active_workflow(): void
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $action = new ActivateWorkflowAction();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowAlreadyActive()->getMessage());

        $action->handle($workflow);
    }
}
