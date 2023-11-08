<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\Workflows;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflowable\Actions\Workflows\SaveWorkflowAction;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Tests\TestCase;

class SaveWorkflowActionTest extends TestCase
{
    use DatabaseTransactions;

    public WorkflowEvent $workflowEvent;

    public WorkflowPriority $workflowPriority;

    public function setUp(): void
    {
        parent::setUp();

        $this->workflowEvent = WorkflowEvent::factory()->create();
        $this->workflowPriority = WorkflowPriority::factory()->create();
    }

    public function test_that_we_can_create_a_new_workflow()
    {
        SaveWorkflowAction::make()
            ->handle(
                'Test Workflow',
                $this->workflowEvent,
                $this->workflowPriority,
                300
            );

        $this->assertDatabaseHas(Workflow::class, [
            'name' => 'Test Workflow',
            'workflow_event_id' => $this->workflowEvent->id,
            'workflow_priority_id' => $this->workflowPriority->id,
            'retry_interval' => 300,
        ]);
    }

    public function test_that_we_can_update_an_existing_workflow()
    {
        $workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowPriority($this->workflowPriority)
            ->create();

        SaveWorkflowAction::make()
            ->withWorkflow($workflow)
            ->handle(
                'Test Workflow',
                $this->workflowEvent,
                $this->workflowPriority,
                300
            );

        $this->assertDatabaseHas(Workflow::class, [
            'id' => $workflow->id,
            'name' => 'Test Workflow',
            'workflow_event_id' => $this->workflowEvent->id,
            'workflow_priority_id' => $this->workflowPriority->id,
            'retry_interval' => 300,
        ]);
    }
}
