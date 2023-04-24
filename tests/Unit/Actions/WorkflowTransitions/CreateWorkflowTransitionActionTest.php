<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\Workflow\Actions\WorkflowTransitions\CreateWorkflowTransitionAction;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class CreateWorkflowTransitionActionTest extends TestCase
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

    public function test_that

    public function test_it_can_create_a_workflow_transition()
    {
        $data = $this->getData();
        extract($data);

        $action = new CreateWorkflowTransitionAction();

        $workflowTransition = $action->handle(
            $workflow,
            $fromWorkflowStep,
            $toWorkflowStep,
            'Test Workflow Transition',
            1
        );

        $this->assertDatabaseHas('workflow_transitions', [
            'id' => $workflowTransition->id,
            'workflow_id' => $workflow->id,
            'from_workflow_step_id' => $fromWorkflowStep->id,
            'to_workflow_step_id' => $toWorkflowStep->id,
            'friendly_name' => 'Test Workflow Transition',
            'ordinal' => 1,
        ]);
    }
}
