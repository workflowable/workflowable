<?php

namespace Workflowable\Workflowable\Tests\Actions\Workflows;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Actions\Workflows\DeactivateWorkflowAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\Workflows\WorkflowDeactivated;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class DeactivateWorkflowActionTest extends TestCase
{
    use HasWorkflowProcess;

    /**
     * Test that an active workflow can be deactivated successfully
     *
     * @throws WorkflowException
     */
    public function test_can_deactivate_active_workflow(): void
    {
        Event::fake();
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $result = DeactivateWorkflowAction::make()->handle($workflow);

        $this->assertEquals(WorkflowStatusEnum::DEACTIVATED, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatusEnum::DEACTIVATED,
        ]);

        Event::assertDispatched(WorkflowDeactivated::class, function ($event) use ($result) {
            return $event->workflow->id === $result->id;
        });
    }

    /**
     * Test that a deactivated workflow cannot be deactivated again
     */
    public function test_cannot_deactivate_already_deactivated_workflow(): void
    {
        Event::fake();
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED)
            ->create();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowAlreadyDeactivated()->getMessage());

        DeactivateWorkflowAction::make()->handle($workflow);
    }
}
