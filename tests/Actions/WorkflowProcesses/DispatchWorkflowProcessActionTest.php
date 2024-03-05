<?php

namespace Workflowable\Workflowable\Tests\Actions\WorkflowProcesses;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Actions\WorkflowProcesses\DispatchWorkflowProcessAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class DispatchWorkflowProcessActionTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_dispatch_a_workflow_process()
    {
        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $workflowProcess = DispatchWorkflowProcessAction::make()->handle($this->workflowProcess);

        $this->assertInstanceOf(WorkflowProcess::class, $workflowProcess);
        $this->assertEquals(WorkflowProcessStatusEnum::DISPATCHED, $workflowProcess->workflow_process_status_id);
        $this->assertEquals($this->workflow->id, $workflowProcess->workflow_id);

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'workflow_process_status_id' => WorkflowProcessStatusEnum::DISPATCHED,
            'workflow_id' => $this->workflow->id,
        ]);
    }
}
