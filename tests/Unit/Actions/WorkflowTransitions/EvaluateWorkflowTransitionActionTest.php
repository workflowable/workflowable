<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowTransitions;

use Mockery\MockInterface;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflowable\Actions\WorkflowTransitions\EvaluateWorkflowTransitionAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowProcess;
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
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
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
            ->withWorkflowProcessStatus(WorkflowProcessStatusEnum::PENDING)
            ->withLastWorkflowActivity($fromWorkflowActivity)
            ->create();

        $isPassing = EvaluateWorkflowTransitionAction::make()->handle($workflowProcess, $workflowTransition);

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
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
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
            ->withWorkflowProcessStatus(WorkflowProcessStatusEnum::PENDING)
            ->withLastWorkflowActivity($fromWorkflowActivity)
            ->create();

        $workflowConditionType = WorkflowConditionType::factory()
            ->withContract(new WorkflowConditionTypeFake())
            ->create();

        WorkflowCondition::factory()
            ->withWorkflowTransition($workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        $eventCondition = \Mockery::mock(WorkflowConditionTypeFake::class)
            ->shouldReceive('handle')
            ->andReturn(false)
            ->getMock();

        GetWorkflowConditionTypeImplementationAction::fake(function (MockInterface $mock) use ($eventCondition) {
            $mock->shouldReceive('handle')
                ->andReturn($eventCondition);
        });

        $isPassing = EvaluateWorkflowTransitionAction::make()->handle($workflowProcess, $workflowTransition);
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
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
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
            ->withWorkflowProcessStatus(WorkflowProcessStatusEnum::PENDING)
            ->withLastWorkflowActivity($fromWorkflowActivity)
            ->create();

        $workflowConditionType = WorkflowConditionType::factory()
            ->withContract(new WorkflowConditionTypeFake())
            ->create();

        WorkflowCondition::factory()
            ->withWorkflowTransition($workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        $action = EvaluateWorkflowTransitionAction::make();

        $eventCondition = \Mockery::mock(WorkflowConditionTypeFake::class)
            ->shouldReceive('handle')
            ->andReturn(true)
            ->getMock();

        GetWorkflowConditionTypeImplementationAction::fake(function (MockInterface $mock) use ($eventCondition) {
            $mock->shouldReceive('handle')
                ->andReturn($eventCondition);
        });

        $isPassing = $action->handle($workflowProcess, $workflowTransition);
        $this->assertTrue($isPassing);
    }
}
