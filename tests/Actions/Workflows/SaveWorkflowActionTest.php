<?php

namespace Workflowable\Workflowable\Tests\Actions\Workflows;

use Workflowable\Workflowable\Actions\Workflows\SaveWorkflowAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class SaveWorkflowActionTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_create_a_new_workflow()
    {
        $workflowPriority = WorkflowPriority::factory()->create();

        SaveWorkflowAction::make()
            ->handle(
                'Test Workflow',
                $this->workflowEvent,
                $workflowPriority,
                500
            );

        $this->assertDatabaseHas(Workflow::class, [
            'name' => 'Test Workflow',
            'workflow_event_id' => $this->workflowEvent->id,
            'workflow_priority_id' => $workflowPriority->id,
            'retry_interval' => 500,
        ]);
    }

    public function test_that_we_can_update_an_existing_workflow()
    {
        $workflowPriority = WorkflowPriority::factory()->create();

        $workflow = Workflow::factory()
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowPriority($workflowPriority)
            ->create();

        SaveWorkflowAction::make()
            ->withWorkflow($workflow)
            ->handle(
                'Test Workflow',
                $this->workflowEvent,
                $workflowPriority,
                500
            );

        $this->assertDatabaseHas(Workflow::class, [
            'id' => $workflow->id,
            'name' => 'Test Workflow',
            'workflow_event_id' => $this->workflowEvent->id,
            'workflow_priority_id' => $workflowPriority->id,
            'retry_interval' => 500,
        ]);
    }
}
