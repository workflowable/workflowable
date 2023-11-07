<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\Workflows;

use Workflowable\Workflowable\Actions\Workflows\SwapWorkflowAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class SwapWorkflowActionTest extends TestCase
{
    /**
     * Test that swapping active workflows works as expected
     *
     * @return void
     */
    public function test_swap_active_workflows()
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow1 = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED)
            ->create();

        $workflow2 = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $result = SwapWorkflowAction::make()->handle($workflow2, $workflow1);

        $this->assertEquals($workflow1->id, $result->id);
        $this->assertEquals(WorkflowStatusEnum::ACTIVE, $result->workflow_status_id);

        $this->assertDatabaseHas('workflows', [
            'id' => $workflow1->id,
            'workflow_status_id' => WorkflowStatusEnum::ACTIVE,
        ]);

        $this->assertDatabaseHas('workflows', [
            'id' => $workflow2->id,
            'workflow_status_id' => WorkflowStatusEnum::DEACTIVATED,
        ]);
    }
}
