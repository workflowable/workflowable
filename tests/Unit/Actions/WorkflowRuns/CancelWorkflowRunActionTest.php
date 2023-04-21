<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowRuns;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Workflowable\Workflow\Actions\WorkflowRuns\CancelWorkflowRunAction;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCancelled;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class CancelWorkflowRunActionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_cancel_a_pending_workflow_run()
    {
        Event::fake();

        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
        ]);

        // Call the action to cancel the workflow run
        $action = new CancelWorkflowRunAction();
        $cancelledWorkflowRun = $action->handle($workflowRun);

        // Assert that the workflow run was cancelled
        $this->assertEquals(WorkflowRunStatus::CANCELLED, $cancelledWorkflowRun->workflow_run_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowRunCancelled::class, function ($event) use ($workflowRun) {
            return $event->workflowRun->id === $workflowRun->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_if_workflow_run_is_not_pending()
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::COMPLETED,
        ]);

        // Call the action to cancel the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not pending');

        $action = new CancelWorkflowRunAction();
        $action->handle($workflowRun);
    }
}
