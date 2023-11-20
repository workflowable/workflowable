<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowProcesses;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Actions\WorkflowProcesses\PauseWorkflowProcessAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessPaused;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcess;

class PauseWorkflowProcessActionTest extends TestCase
{
    use HasWorkflowProcess;

    /** @test */
    public function it_should_pause_a_pending_workflow_run()
    {
        Event::fake();

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::PENDING,
        ]);

        // Call the action to pause the workflow run
        $pausedWorkflowRun = PauseWorkflowProcessAction::make()->handle($this->workflowProcess);

        // Assert that the workflow run was paused
        $this->assertEquals(WorkflowProcessStatusEnum::PAUSED, $pausedWorkflowRun->workflow_process_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowProcessPaused::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_pausing_if_workflow_run_is_not_pending()
    {
        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::COMPLETED,
        ]);

        // Call the action to pause the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow process is not pending');

        PauseWorkflowProcessAction::make()->handle($this->workflowProcess);
    }
}
