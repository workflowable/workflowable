<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\Workflow\Actions\WorkflowTransitions\UpdateWorkflowTransitionAction;
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
    public function getData(): array
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

        return [
            'workflowEvent' => $workflowEvent,
            'workflow' => $workflow,
            'fromWorkflowStep' => $workflowSteps[0],
            'toWorkflowStep' => $workflowSteps[1],
        ];
    }

    public function test_that_we_can_update_the_name_of_the_transition()
    {

    }

    public function test_that_we_can_update_the_ordinal_of_the_transition()
    {

    }

    public function test_that_we_can_change_the_from_workflow_step()
    {
        extract($this->getData());

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStep)
            ->withToWorkflowStep($toWorkflowStep)
            ->create();

        // Ensure our data is as expected
        $this->assertDatabaseHas(WorkflowTransition::class, [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_step_id' => $fromWorkflowStep->id,
            'to_workflow_step_id' => $toWorkflowStep->id,
        ]);



        /** @var UpdateWorkflowTransitionAction $action */
        $action = app(UpdateWorkflowTransitionAction::class);
        $action->handle($workflowTransition, 'A New Name', 5)
    }

    public function test_that_we_can_change_the_to_workflow_step()
    {

    }

    public function test_that_we_will_delete_all_preexisting_workflow_transition_conditions_and_create_new_transitions()
    {

    }
}
