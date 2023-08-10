<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\Workflowable\Actions\WorkflowTransitions\UpdateWorkflowTransitionAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class UpdateWorkflowTransitionActionTest extends TestCase
{
    public function test_that_we_can_change_the_from_workflow_activity()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowActivities = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->count(4)
            ->create();

        $fromWorkflowActivityOne = $workflowActivities[0];
        $fromWorkflowActivityTwo = $workflowActivities[1];
        $toWorkflowActivityOne = $workflowActivities[2];
        $toWorkflowActivityTwo = $workflowActivities[3];

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivityOne)
            ->withToWorkflowActivity($toWorkflowActivityOne)
            ->create();

        // Ensure our data is as expected
        $this->assertDatabaseHas(WorkflowTransition::class, [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_activity_id' => $fromWorkflowActivityOne->id,
            'to_workflow_activity_id' => $toWorkflowActivityOne->id,
        ]);

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflow->id,
            'from_workflow_activity' => $fromWorkflowActivityTwo,
            'to_workflow_activity' => $toWorkflowActivityTwo,
            'name' => 'Test Workflow Transition2',
            'ordinal' => 2,
            'ux_uuid' => $workflowTransition->ux_uuid,
        ]);

        $action = new UpdateWorkflowTransitionAction();
        $action->handle($workflowTransition, $workflowTransitionData);

        $this->assertDatabaseHas(WorkflowTransition::class, [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_activity_id' => $fromWorkflowActivityTwo->id,
            'to_workflow_activity_id' => $toWorkflowActivityTwo->id,
            'name' => 'Test Workflow Transition2',
            'ordinal' => 2,
            'ux_uuid' => $workflowTransition->ux_uuid,
        ]);
    }

    public function test_that_we_cannot_modify_a_transition_belonging_to_a_workflow_that_is_not_a_draft()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $workflowActivities = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->count(4)
            ->create();

        $fromWorkflowActivitiesOne = $workflowActivities[0];
        $fromWorkflowActivityTwo = $workflowActivities[1];
        $toWorkflowActivityOne = $workflowActivities[2];
        $toWorkflowActivityTwo = $workflowActivities[3];

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivitiesOne)
            ->withToWorkflowActivity($toWorkflowActivityOne)
            ->create();

        // Ensure our data is as expected
        $this->assertDatabaseHas(WorkflowTransition::class, [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_activity_id' => $fromWorkflowActivitiesOne->id,
            'to_workflow_activity_id' => $toWorkflowActivityOne->id,
        ]);

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflow->id,
            'from_workflow_activity' => $fromWorkflowActivityTwo,
            'to_workflow_activity' => $toWorkflowActivityTwo,
            'name' => 'Test Workflow Transition2',
            'ordinal' => 2,
            'ux_uuid' => $workflowTransition->ux_uuid,
        ]);

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotModifyWorkflowNotInDraftState()->getMessage());
        $action = new UpdateWorkflowTransitionAction();
        $action->handle($workflowTransition, $workflowTransitionData);
    }

    public function test_that_we_cannot_use_a_from_workflow_activity_that_does_not_belong_to_the_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowOne = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowTwo = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflowOne)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflowOne)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

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
        $action = new UpdateWorkflowTransitionAction();
        $action->handle($workflowTransition, $workflowTransitionData);
    }

    public function test_that_we_cannot_use_a_to_workflow_activity_that_does_not_belong_to_the_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflowOne = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowTwo = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflowTwo)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflowOne)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflowOne)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

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
        $action = new UpdateWorkflowTransitionAction();
        $action->handle($workflowTransition, $workflowTransitionData);

    }
}
