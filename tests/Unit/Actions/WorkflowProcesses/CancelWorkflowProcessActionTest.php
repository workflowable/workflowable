<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowProcesses;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Actions\WorkflowProcesses\CancelWorkflowProcessAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCancelled;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcess;

class CancelWorkflowProcessActionTest extends TestCase
{
    use HasWorkflowProcess;

    /** @test */
    public function it_should_cancel_a_pending_workflow_run()
    {
        Event::fake();

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::PENDING,
        ]);

        // Call the action to cancel the workflow run
        $cancelledWorkflowRun = CancelWorkflowProcessAction::make()->handle($this->workflowProcess);

        // Assert that the workflow run was cancelled
        $this->assertEquals(WorkflowProcessStatusEnum::CANCELLED, $cancelledWorkflowRun->workflow_process_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowProcessCancelled::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_cancelling_if_workflow_run_is_not_pending()
    {
        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::COMPLETED,
        ]);

        // Call the action to cancel the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not pending');

        CancelWorkflowProcessAction::make()->handle($this->workflowProcess);
    }
}
