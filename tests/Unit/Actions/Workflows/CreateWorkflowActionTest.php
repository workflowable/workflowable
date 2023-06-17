<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\Workflows;

use Workflowable\Workflow\Actions\Workflows\CreateWorkflowAction;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class CreateWorkflowActionTest extends TestCase
{
    public function test_that_we_can_create_a_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = (new CreateWorkflowAction())->handle('Test Workflow', $workflowEvent, 60);
        $this->assertInstanceOf(Workflow::class, $workflow);

        $this->assertDatabaseHas(Workflow::class, [
            'name' => 'Test Workflow',
            'workflow_event_id' => $workflowEvent->id,
            'workflow_status_id' => WorkflowStatus::DRAFT,
            'retry_interval' => 60,
        ]);
    }
}
