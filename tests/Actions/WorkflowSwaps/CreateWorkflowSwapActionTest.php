<?php

namespace Workflowable\Workflowable\Tests\Actions\WorkflowSwaps;

use Workflowable\Workflowable\Actions\WorkflowSwaps\CreateWorkflowSwapAction;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Models\WorkflowSwapActivityMap;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class CreateWorkflowSwapActionTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_create_a_new_workflow_swap()
    {
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        $fromWorkflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();
        $toWorkflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflow($fromWorkflow)
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->create();

        $workflowSwap = CreateWorkflowSwapAction::make()->handle($fromWorkflow, $toWorkflow);

        $this->assertInstanceOf(WorkflowSwap::class, $workflowSwap);
        $this->assertDatabaseCount(WorkflowSwap::class, 1);
        $this->assertDatabaseHas(WorkflowSwap::class, [
            'from_workflow_id' => $fromWorkflow->id,
            'to_workflow_id' => $toWorkflow->id,
            'workflow_swap_status_id' => WorkflowSwapStatusEnum::Draft,
        ]);

        $this->assertDatabaseHas(WorkflowSwapActivityMap::class, [
            'from_workflow_activity_id' => $fromWorkflowActivity->id,
            'to_workflow_activity_id' => null,
            'workflow_swap_id' => $workflowSwap->id,
        ]);
    }

    public function test_that_a_workflow_swap_must_be_between_two_workflows_belonging_to_the_same_workflow_event()
    {
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        $workflowEventBogus = WorkflowEvent::factory()->create(['class_name' => 'bogus']);

        $fromWorkflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();
        $toWorkflow = Workflow::factory()->withWorkflowEvent($workflowEventBogus)->create();

        WorkflowActivity::factory()
            ->withWorkflow($fromWorkflow)
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->create();

        $this->expectException(WorkflowSwapException::class);
        $this->expectExceptionMessage(WorkflowSwapException::cannotPerformSwapBetweenWorkflowsOfDifferentEvents()->getMessage());
        CreateWorkflowSwapAction::make()->handle($fromWorkflow, $toWorkflow);
    }
}
