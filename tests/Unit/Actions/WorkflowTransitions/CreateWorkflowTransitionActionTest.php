<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\Workflow\Actions\WorkflowTransitions\CreateWorkflowTransitionAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class CreateWorkflowTransitionActionTest extends TestCase
{
    public function test_it_can_create_a_workflow_transition()
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
        $this->markTestSkipped('Not implemented yet');

        // WorkflowException::cannotModifyWorkflowNotInDraftState();
    }

    public function test_that_we_cannot_use_a_from_workflow_step_that_does_not_belong_to_the_workflow()
    {
        $this->markTestSkipped('Not implemented yet');

        // WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
    }

    public function test_that_we_cannot_use_a_to_workflow_step_that_does_not_belong_to_the_workflow()
    {
        $this->markTestSkipped('Not implemented yet');

        // WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
    }
}
