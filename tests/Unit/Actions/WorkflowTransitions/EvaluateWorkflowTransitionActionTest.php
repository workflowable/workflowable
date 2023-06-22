<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\WorkflowTransitions;

use Mockery\MockInterface;
use Workflowable\WorkflowEngine\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\WorkflowEngine\Actions\WorkflowTransitions\EvaluateWorkflowTransitionAction;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowCondition;
use Workflowable\WorkflowEngine\Models\WorkflowConditionType;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\WorkflowEngine\Tests\TestCase;

class EvaluateWorkflowTransitionActionTest extends TestCase
{
    public function test_that_a_transition_with_no_conditions_passes()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $fromWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStep)
            ->withToWorkflowStep($toWorkflowStep)
            ->create();

        $workflowRun = WorkflowRun::factory()
            ->withWorkflow($workflow)
            ->withWorkflowRunStatus(WorkflowStatus::ACTIVE)
            ->withLastWorkflowStep($fromWorkflowStep)
            ->create();

        /** @var EvaluateWorkflowTransitionAction $action */
        $action = app(EvaluateWorkflowTransitionAction::class);

        $isPassing = $action->handle($workflowRun, $workflowTransition);

        $this->assertTrue($isPassing);
    }

    public function test_it_can_evaluate_a_workflow_transition_that_has_passing_conditions_correctly()
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $fromWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStep)
            ->withToWorkflowStep($toWorkflowStep)
            ->create();

        $workflowRun = WorkflowRun::factory()
            ->withWorkflow($workflow)
            ->withWorkflowRunStatus(WorkflowStatus::ACTIVE)
            ->withLastWorkflowStep($fromWorkflowStep)
            ->create();

        $workflowConditionType = WorkflowConditionType::factory()
            ->withContract(new WorkflowConditionTypeFake())
            ->create();

        WorkflowCondition::factory()
            ->withWorkflowTransition($workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        /** @var EvaluateWorkflowTransitionAction $action */
        $action = app(EvaluateWorkflowTransitionAction::class);

        $eventCondition = \Mockery::mock(WorkflowConditionTypeFake::class)
            ->shouldReceive('handle')
            ->andReturn(false)
            ->getMock();

        $this->partialMock(GetWorkflowConditionTypeImplementationAction::class, function (MockInterface $mock) use ($eventCondition) {
            $mock->shouldReceive('handle')
                ->andReturn($eventCondition);
        });

        $isPassing = $action->handle($workflowRun, $workflowTransition);
        $this->assertFalse($isPassing);
    }

    public function test_it_can_evaluate_a_workflow_transition_with_failing_conditions_correctly()
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $fromWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStep)
            ->withToWorkflowStep($toWorkflowStep)
            ->create();

        $workflowRun = WorkflowRun::factory()
            ->withWorkflow($workflow)
            ->withWorkflowRunStatus(WorkflowStatus::ACTIVE)
            ->withLastWorkflowStep($fromWorkflowStep)
            ->create();

        $workflowConditionType = WorkflowConditionType::factory()
            ->withContract(new WorkflowConditionTypeFake())
            ->create();

        WorkflowCondition::factory()
            ->withWorkflowTransition($workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        /** @var EvaluateWorkflowTransitionAction $action */
        $action = app(EvaluateWorkflowTransitionAction::class);

        $eventCondition = \Mockery::mock(WorkflowConditionTypeFake::class)
            ->shouldReceive('handle')
            ->andReturn(true)
            ->getMock();

        $this->partialMock(GetWorkflowConditionTypeImplementationAction::class, function (MockInterface $mock) use ($eventCondition) {
            $mock->shouldReceive('handle')
                ->andReturn($eventCondition);
        });

        $isPassing = $action->handle($workflowRun, $workflowTransition);
        $this->assertTrue($isPassing);
    }
}
