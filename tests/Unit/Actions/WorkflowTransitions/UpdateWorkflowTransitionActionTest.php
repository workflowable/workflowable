<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\WorkflowEngine\Actions\WorkflowTransitions\UpdateWorkflowTransitionAction;
use Workflowable\WorkflowEngine\DataTransferObjects\WorkflowTransitionData;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Exceptions\WorkflowStepException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\WorkflowEngine\Tests\TestCase;

class UpdateWorkflowTransitionActionTest extends TestCase
{
    public function test_that_we_can_change_the_from_workflow_step()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $workflowSteps = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->count(4)
            ->create();

        $fromWorkflowStepOne = $workflowSteps[0];
        $fromWorkflowStepTwo = $workflowSteps[1];
        $toWorkflowStepOne = $workflowSteps[2];
        $toWorkflowStepTwo = $workflowSteps[3];

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStepOne)
            ->withToWorkflowStep($toWorkflowStepOne)
            ->create();

        // Ensure our data is as expected
        $this->assertDatabaseHas(WorkflowTransition::class, [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_step_id' => $fromWorkflowStepOne->id,
            'to_workflow_step_id' => $toWorkflowStepOne->id,
        ]);

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflow->id,
            'from_workflow_step' => $fromWorkflowStepTwo,
            'to_workflow_step' => $toWorkflowStepTwo,
            'name' => 'Test Workflow Transition2',
            'ordinal' => 2,
            'ux_uuid' => $workflowTransition->ux_uuid,
        ]);

        $action = new UpdateWorkflowTransitionAction();
        $action->handle($workflowTransition, $workflowTransitionData);

        $this->assertDatabaseHas(WorkflowTransition::class, [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_step_id' => $fromWorkflowStepTwo->id,
            'to_workflow_step_id' => $toWorkflowStepTwo->id,
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
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $workflowSteps = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->count(4)
            ->create();

        $fromWorkflowStepOne = $workflowSteps[0];
        $fromWorkflowStepTwo = $workflowSteps[1];
        $toWorkflowStepOne = $workflowSteps[2];
        $toWorkflowStepTwo = $workflowSteps[3];

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStepOne)
            ->withToWorkflowStep($toWorkflowStepOne)
            ->create();

        // Ensure our data is as expected
        $this->assertDatabaseHas(WorkflowTransition::class, [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_step_id' => $fromWorkflowStepOne->id,
            'to_workflow_step_id' => $toWorkflowStepOne->id,
        ]);

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflow->id,
            'from_workflow_step' => $fromWorkflowStepTwo,
            'to_workflow_step' => $toWorkflowStepTwo,
            'name' => 'Test Workflow Transition2',
            'ordinal' => 2,
            'ux_uuid' => $workflowTransition->ux_uuid,
        ]);

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotModifyWorkflowNotInDraftState()->getMessage());
        $action = new UpdateWorkflowTransitionAction();
        $action->handle($workflowTransition, $workflowTransitionData);
    }

    public function test_that_we_cannot_use_a_from_workflow_step_that_does_not_belong_to_the_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $workflowOne = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $workflowTwo = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $fromWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflowOne)
            ->create();
        $toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflowOne)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStep)
            ->withToWorkflowStep($toWorkflowStep)
            ->create();

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflowTwo->id,
            'from_workflow_step' => $fromWorkflowStep,
            'to_workflow_step' => $toWorkflowStep,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);

        $this->expectException(WorkflowStepException::class);
        $this->expectExceptionMessage(WorkflowStepException::workflowStepDoesNotBelongToWorkflow()->getMessage());
        $action = new UpdateWorkflowTransitionAction();
        $action->handle($workflowTransition, $workflowTransitionData);
    }

    public function test_that_we_cannot_use_a_to_workflow_step_that_does_not_belong_to_the_workflow()
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

        $fromWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflowTwo)
            ->create();
        $toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflowOne)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflowOne)
            ->withFromWorkflowStep($fromWorkflowStep)
            ->withToWorkflowStep($toWorkflowStep)
            ->create();

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflowTwo->id,
            'from_workflow_step' => $fromWorkflowStep,
            'to_workflow_step' => $toWorkflowStep,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);

        $this->expectException(WorkflowStepException::class);
        $this->expectExceptionMessage(WorkflowStepException::workflowStepDoesNotBelongToWorkflow()->getMessage());
        $action = new UpdateWorkflowTransitionAction();
        $action->handle($workflowTransition, $workflowTransitionData);

    }
}
