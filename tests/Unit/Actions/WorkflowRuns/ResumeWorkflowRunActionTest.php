<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowRuns;

use Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflow\Actions\WorkflowRuns\ResumeWorkflowRunAction;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunResumed;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class ResumeWorkflowRunActionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_should_resume_a_paused_workflow_run()
    {
        \Illuminate\Support\Facades\Event::fake();

        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake(['test' => []]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::PAUSED,
        ]);

        // Call the action to resume the workflow run
        $action = new ResumeWorkflowRunAction();
        $resumedWorkflowRun = $action->handle($workflowRun);

        // Assert that the workflow run was resumed
        $this->assertEquals(WorkflowRunStatus::PENDING, $resumedWorkflowRun->workflow_run_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowRunResumed::class, function ($event) use ($workflowRun) {
            return $event->workflowRun->id === $workflowRun->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_if_workflow_run_is_not_paused()
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::CANCELLED,
        ]);

        // Call the action to resume the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not paused');

        $action = new ResumeWorkflowRunAction();
        $action->handle($workflowRun);
    }
}
