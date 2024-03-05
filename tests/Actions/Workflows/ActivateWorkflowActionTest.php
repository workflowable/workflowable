<?php

namespace Workflowable\Workflowable\Tests\Actions\Workflows;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Actions\Workflows\ActivateWorkflowAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\Workflows\WorkflowActivated;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class ActivateWorkflowActionTest extends TestCase
{
    use HasWorkflowProcess;

    /**
     * Test that a deactivated workflow can be activated successfully
     */
    public function test_can_activate_deactivated_workflow(): void
    {
        Event::fake();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED)
            ->create();

        $result = ActivateWorkflowAction::make()->handle($workflow);

        $this->assertEquals(WorkflowStatusEnum::ACTIVE, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatusEnum::ACTIVE,
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
        $workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowAlreadyActive()->getMessage());

        ActivateWorkflowAction::make()->handle($workflow);
    }
}
