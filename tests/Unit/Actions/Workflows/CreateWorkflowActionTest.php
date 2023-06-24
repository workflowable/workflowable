<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\Workflows;

use Workflowable\WorkflowEngine\Actions\Workflows\CreateWorkflowAction;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\TestCase;
use Workflowable\WorkflowEngine\Models\WorkflowPriority;

class CreateWorkflowActionTest extends TestCase
{
    public function test_that_we_can_create_a_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowPriority = WorkflowPriority::factory()->create();

        $workflow = (new CreateWorkflowAction())->handle('Test Workflow', $workflowEvent, $workflowPriority, 60);
        $this->assertInstanceOf(Workflow::class, $workflow);

        $this->assertDatabaseHas(Workflow::class, [
            'name' => 'Test Workflow',
            'workflow_event_id' => $workflowEvent->id,
            'workflow_status_id' => WorkflowStatus::DRAFT,
            'retry_interval' => 60,
        ]);
    }
}
