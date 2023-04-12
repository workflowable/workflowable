<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\Workflows;

use Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflow\Tests\TestCase;
use Workflowable\Workflow\Actions\Workflows\DeactivateWorkflowAction;
use Workflowable\Workflow\Events\Workflows\WorkflowDeactivated;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;

class DeactivateWorkflowActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that an active workflow can be deactivated successfully
     *
     * @throws WorkflowException
     */
    public function test_can_deactivate_active_workflow(): void
    {
        Event::fake();
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake('test'))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $action = new DeactivateWorkflowAction();

        $result = $action->handle($workflow);

        $this->assertEquals(WorkflowStatus::INACTIVE, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatus::INACTIVE,
        ]);

        Event::assertDispatched(WorkflowDeactivated::class, function ($event) use ($result) {
            return $event->workflow->id === $result->id;
        });
    }

    /**
     * Test that an inactive workflow cannot be deactivated again
     */
    public function test_cannot_deactivate_already_inactive_workflow(): void
    {
        Event::fake();
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake('test'))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::INACTIVE)
            ->create();

        $action = new DeactivateWorkflowAction();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowAlreadyInactive()->getMessage());

        $action->handle($workflow);
    }
}
