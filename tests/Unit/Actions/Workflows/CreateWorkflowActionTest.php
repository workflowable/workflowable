<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\Workflows;

use Workflowable\Workflowable\Actions\Workflows\CreateWorkflowAction;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

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
