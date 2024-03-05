<?php

namespace Workflowable\Workflowable\Tests\Actions\WorkflowProcesses;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Actions\WorkflowProcesses\CreateWorkflowProcessAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class CreateWorkflowProcessActionTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_create_a_workflow_process()
    {
        $workflowEventContract = new WorkflowEventFake([
            'test' => 'Test',
        ]);

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $workflowRun = CreateWorkflowProcessAction::make()->handle($this->workflow, $workflowEventContract);
        $this->assertInstanceOf(WorkflowProcess::class, $workflowRun);
        $this->assertEquals(WorkflowProcessStatusEnum::CREATED, $workflowRun->workflow_process_status_id);
        $this->assertEquals($this->workflow->id, $workflowRun->workflow_id);

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'workflow_process_status_id' => WorkflowProcessStatusEnum::CREATED,
            'workflow_id' => $this->workflow->id,
        ]);

        $this->assertDatabaseHas(WorkflowProcessToken::class, [
            'workflow_process_id' => $workflowRun->id,
            'key' => 'test',
            'value' => 'Test',
        ]);
    }
}
