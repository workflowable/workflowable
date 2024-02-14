<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowTransitions;

use Mockery\MockInterface;
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
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

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
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

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

        $workflowConditionType = WorkflowConditionType::query()->where('class_name', WorkflowConditionTypeFake::class)->firstOrFail();

        WorkflowCondition::factory()
            ->withWorkflowTransition($workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        $this->partialMock(WorkflowConditionTypeFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->andReturn(false);
        });

        $isPassing = EvaluateWorkflowTransitionAction::make()->handle($workflowProcess, $workflowTransition);
        $this->assertFalse($isPassing);
    }

    public function test_it_can_evaluate_a_workflow_transition_with_failing_conditions_correctly()
    {
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

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

        $workflowConditionType = WorkflowConditionType::query()->where('class_name', WorkflowConditionTypeFake::class)->firstOrFail();

        WorkflowCondition::factory()
            ->withWorkflowTransition($workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        $action = EvaluateWorkflowTransitionAction::make();

        \Mockery::mock(WorkflowConditionTypeFake::class)
            ->shouldReceive('handle')
            ->andReturn(true)
            ->getMock();

        $isPassing = $action->handle($workflowProcess, $workflowTransition);
        $this->assertTrue($isPassing);
    }
}
