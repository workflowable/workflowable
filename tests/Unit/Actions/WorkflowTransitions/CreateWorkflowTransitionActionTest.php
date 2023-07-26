<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\Workflowable\Actions\WorkflowTransitions\CreateWorkflowTransitionAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class CreateWorkflowTransitionActionTest extends TestCase
{
    public function test_it_can_create_a_workflow_transition()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $workflowActivities = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->count(2)
            ->create();

        $fromWorkflowActivity = $workflowActivities[0];
        $toWorkflowActivity = $workflowActivities[1];

        $action = new CreateWorkflowTransitionAction();

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflow->id,
            'from_workflow_activity' => $fromWorkflowActivity,
            'to_workflow_activity' => $toWorkflowActivity,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);

        $workflowTransition = $action->handle($workflowTransitionData);

        $this->assertDatabaseHas('workflow_transitions', [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_activity_id' => $fromWorkflowActivity->id,
            'to_workflow_activity_id' => $toWorkflowActivity->id,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);
    }

    public function test_that_we_cannot_create_a_transition_belonging_to_a_workflow_that_is_not_a_draft()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $workflowActivities = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->count(2)
            ->create();

        $fromWorkflowActivity = $workflowActivities[0];
        $toWorkflowActivity = $workflowActivities[1];

        $action = new CreateWorkflowTransitionAction();

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflow->id,
            'from_workflow_activity' => $fromWorkflowActivity,
            'to_workflow_activity' => $toWorkflowActivity,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotModifyWorkflowNotInDraftState()->getMessage());
        $action->handle($workflowTransitionData);
    }

    public function test_that_we_cannot_use_a_from_workflow_activity_that_does_not_belong_to_the_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowOne = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $workflowTwo = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflowOne)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflowOne)
            ->create();

        $action = new CreateWorkflowTransitionAction();

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflowTwo->id,
            'from_workflow_activity' => $fromWorkflowActivity,
            'to_workflow_activity' => $toWorkflowActivity,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);

        $this->expectException(WorkflowActivityException::class);
        $this->expectExceptionMessage(WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow()->getMessage());
        $action->handle($workflowTransitionData);

        // WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow();
    }

    public function test_that_we_cannot_use_a_to_workflow_activity_that_does_not_belong_to_the_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowOne = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $workflowTwo = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflowTwo)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflowOne)
            ->create();

        $action = new CreateWorkflowTransitionAction();

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflowTwo->id,
            'from_workflow_activity' => $fromWorkflowActivity,
            'to_workflow_activity' => $toWorkflowActivity,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);

        $this->expectException(WorkflowActivityException::class);
        $this->expectExceptionMessage(WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow()->getMessage());
        $action->handle($workflowTransitionData);
    }
}
