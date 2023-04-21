<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\Workflows;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Workflowable\Workflow\Actions\Workflows\ArchiveWorkflowAction;
use Workflowable\Workflow\Events\Workflows\WorkflowArchived;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class ArchiveWorkflowActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that an inactive workflow can be archived successfully
     */
    public function test_can_archive_inactive_workflow(): void
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

        $action = new ArchiveWorkflowAction();

        $result = $action->handle($workflow);

        $this->assertEquals(WorkflowStatus::ARCHIVED, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatus::ARCHIVED,
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
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $action = new ArchiveWorkflowAction();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowCannotBeArchivedFromActiveState()->getMessage());

        $action->handle($workflow);
    }

    /**
     * Test that a workflow cannot be archived if it has active runs
     */
    public function test_cannot_archive_workflow_with_active_runs(): void
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::INACTIVE)
            ->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
        ]);

        $action = new ArchiveWorkflowAction();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotArchiveWorkflowWithActiveRuns()->getMessage());

        $action->handle($workflow);
    }
}
