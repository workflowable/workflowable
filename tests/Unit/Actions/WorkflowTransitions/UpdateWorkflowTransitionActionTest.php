<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\Workflow\Actions\WorkflowTransitions\UpdateWorkflowTransitionAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

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
        // WorkflowException::cannotModifyWorkflowNotInDraftState();
    }

    public function test_that_we_cannot_use_a_from_workflow_step_that_does_not_belong_to_the_workflow()
    {
        // WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
    }

    public function test_that_we_cannot_use_a_to_workflow_step_that_does_not_belong_to_the_workflow()
    {
        // WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
    }
}
