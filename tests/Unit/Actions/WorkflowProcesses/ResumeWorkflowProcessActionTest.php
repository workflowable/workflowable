<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowProcesses;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Actions\WorkflowProcesses\ResumeWorkflowProcessAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessResumed;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcessTests;

class ResumeWorkflowProcessActionTest extends TestCase
{
    use HasWorkflowProcessTests;

    /** @test */
    public function it_should_resume_a_paused_workflow_run()
    {
        Event::fake();

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::PAUSED,
        ]);

        // Call the action to resume the workflow run
        $resumedWorkflowRun = ResumeWorkflowProcessAction::make()->handle($this->workflowProcess);

        // Assert that the workflow run was resumed
        $this->assertEquals(WorkflowProcessStatusEnum::PENDING, $resumedWorkflowRun->workflow_process_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowProcessResumed::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_resuming_if_workflow_run_is_not_paused()
    {
        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::CANCELLED,
        ]);

        // Call the action to resume the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not paused');

        ResumeWorkflowProcessAction::make()->handle($this->workflowProcess);
    }
}
