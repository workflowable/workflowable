<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowTransitions;

use Mockery\MockInterface;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflowable\Actions\WorkflowTransitions\EvaluateWorkflowTransitionAction;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class EvaluateWorkflowTransitionActionTest extends TestCase
{
    public function test_that_a_transition_with_no_conditions_passes()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

        $workflowProcess = WorkflowProcess::factory()
            ->withWorkflow($workflow)
            ->withWorkflowProcessStatus(WorkflowStatus::ACTIVE)
            ->withLastWorkflowActivity($fromWorkflowActivity)
            ->create();

        /** @var EvaluateWorkflowTransitionAction $action */
        $action = app(EvaluateWorkflowTransitionAction::class);

        $isPassing = $action->handle($workflowProcess, $workflowTransition);

        $this->assertTrue($isPassing);
    }

    public function test_it_can_evaluate_a_workflow_transition_that_has_passing_conditions_correctly()
    {
        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

        $workflowProcess = WorkflowProcess::factory()
            ->withWorkflow($workflow)
            ->withWorkflowProcessStatus(WorkflowStatus::ACTIVE)
            ->withLastWorkflowActivity($fromWorkflowActivity)
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

        $isPassing = $action->handle($workflowProcess, $workflowTransition);
        $this->assertFalse($isPassing);
    }

    public function test_it_can_evaluate_a_workflow_transition_with_failing_conditions_correctly()
    {
        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

        $workflowProcess = WorkflowProcess::factory()
            ->withWorkflow($workflow)
            ->withWorkflowProcessStatus(WorkflowStatus::ACTIVE)
            ->withLastWorkflowActivity($fromWorkflowActivity)
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

        $isPassing = $action->handle($workflowProcess, $workflowTransition);
        $this->assertTrue($isPassing);
    }
}
