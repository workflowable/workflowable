<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\WorkflowEngine\Actions\WorkflowTransitions\CreateWorkflowTransitionAction;
use Workflowable\WorkflowEngine\DataTransferObjects\WorkflowTransitionData;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Exceptions\WorkflowStepException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\WorkflowEngine\Tests\TestCase;

class CreateWorkflowTransitionActionTest extends TestCase
{
    public function test_it_can_create_a_workflow_transition()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $workflowSteps = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->count(2)
            ->create();

        $fromWorkflowStep = $workflowSteps[0];
        $toWorkflowStep = $workflowSteps[1];

        $action = new CreateWorkflowTransitionAction();

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflow->id,
            'from_workflow_step' => $fromWorkflowStep,
            'to_workflow_step' => $toWorkflowStep,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);

        $workflowTransition = $action->handle($workflowTransitionData);

        $this->assertDatabaseHas('workflow_transitions', [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_step_id' => $fromWorkflowStep->id,
            'to_workflow_step_id' => $toWorkflowStep->id,
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

        $workflowSteps = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->count(2)
            ->create();

        $fromWorkflowStep = $workflowSteps[0];
        $toWorkflowStep = $workflowSteps[1];

        $action = new CreateWorkflowTransitionAction();

        $workflowTransitionData = WorkflowTransitionData::fromArray([
            'workflow_id' => $workflow->id,
            'from_workflow_step' => $fromWorkflowStep,
            'to_workflow_step' => $toWorkflowStep,
            'name' => 'Test Workflow Transition',
            'ordinal' => 1,
            'ux_uuid' => 'test-uuid',
        ]);

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotModifyWorkflowNotInDraftState()->getMessage());
        $action->handle($workflowTransitionData);
    }

    public function test_that_we_cannot_use_a_from_workflow_step_that_does_not_belong_to_the_workflow()
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
            ->withWorkflow($workflowOne)
            ->create();
        $toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflowOne)
            ->create();

        $action = new CreateWorkflowTransitionAction();

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
        $action->handle($workflowTransitionData);

        // WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
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

        $action = new CreateWorkflowTransitionAction();

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
        $action->handle($workflowTransitionData);
    }
}
