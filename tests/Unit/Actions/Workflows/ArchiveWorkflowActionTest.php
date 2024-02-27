<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\Workflows;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Actions\Workflows\ArchiveWorkflowAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\Workflows\WorkflowArchived;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcess;

class ArchiveWorkflowActionTest extends TestCase
{
    use HasWorkflowProcess;

    /**
     * Test that a deactivated workflow can be archived successfully
     */
    public function test_can_archive_deactivated_workflow(): void
    {
        Event::fake();

        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED)
            ->create();

        $result = ArchiveWorkflowAction::make()->handle($workflow);

        $this->assertEquals(WorkflowStatusEnum::ARCHIVED, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatusEnum::ARCHIVED,
        ]);

        Event::assertDispatched(WorkflowArchived::class, function ($event) use ($result) {
            return $event->workflow->id === $result->id;
        });
    }

    /**
     * Test that a workflow cannot be archived if it is active
     */
    public function test_cannot_archive_active_workflow(): void
    {
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowCannotBeArchivedFromActiveState()->getMessage());

        ArchiveWorkflowAction::make()->handle($workflow);
    }

    /**
     * Test that a workflow cannot be archived if it has active runs
     */
    public function test_cannot_archive_workflow_with_active_runs(): void
    {
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED->value)
            ->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowProcess::factory()->withWorkflow($workflow)->create([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::PENDING,
        ]);

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotArchiveWorkflowWithActiveProcesses()->getMessage());

        ArchiveWorkflowAction::make()->handle($workflow);
    }
}
