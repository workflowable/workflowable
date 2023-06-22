<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\WorkflowRuns;

use Mockery\MockInterface;
use Workflowable\WorkflowEngine\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\WorkflowEngine\Actions\WorkflowRuns\GetNextStepForWorkflowRunAction;
use Workflowable\WorkflowEngine\Models\WorkflowCondition;
use Workflowable\WorkflowEngine\Models\WorkflowConditionType;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\WorkflowEngine\Tests\TestCase;
use Workflowable\WorkflowEngine\Tests\Traits\HasWorkflowRunTests;

class GetNextStepForWorkflowRunActionTest extends TestCase
{
    use HasWorkflowRunTests;

    public function test_that_when_there_is_no_last_step_we_will_look_for_starter_transitions()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_that_we_can_get_the_next_step_for_a_workflow_run(): void
    {
        /** @var GetNextStepForWorkflowRunAction $getNextStepAction */
        $getNextStepAction = app(GetNextStepForWorkflowRunAction::class);
        $nextWorkflowRunStep = $getNextStepAction->handle($this->workflowRun);

        $this->assertEquals($this->toWorkflowStep->id, $nextWorkflowRunStep->id);
    }

    public function test_that_we_will_prioritize_evaluating_transitions_that_have_a_lower_ordinal_value(): void
    {
        $this->workflowTransition->update(['ordinal' => 2]);

        $prioritizedWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($this->workflow)
            ->create();

        WorkflowTransition::factory()
            ->withWorkflow($this->workflow)
            ->withFromWorkflowStep($this->fromWorkflowStep)
            ->withToWorkflowStep($prioritizedWorkflowStep)
            ->create([
                'ordinal' => 1,
            ]);

        $getNextStepAction = app(GetNextStepForWorkflowRunAction::class);
        $nextWorkflowRunStep = $getNextStepAction->handle($this->workflowRun);

        $this->assertEquals($prioritizedWorkflowStep->id, $nextWorkflowRunStep->id);
    }

    public function test_that_we_will_check_conditions_before_deciding_if_a_transition_may_be_performed(): void
    {
        // Add the condition type to config so that it can be resolved later
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        // Create the step that we want to be prioritized
        $prioritizedWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($this->workflow)
            ->create();

        // Ensure this transition will be processed first
        $this->workflowTransition->update(['ordinal' => 1]);

        // Ensure that it has a higher ordinal value than the other transition so that it will not be prioritized
        WorkflowTransition::factory()
            ->withWorkflow($this->workflow)
            ->withFromWorkflowStep($this->fromWorkflowStep)
            ->withToWorkflowStep($prioritizedWorkflowStep)
            ->create([
                'ordinal' => 2,
            ]);

        // Build out the condition that will be evaluated
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        WorkflowCondition::factory()
            ->withWorkflowTransition($this->workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        // Mock the condition type so that it will return false
        $eventCondition = \Mockery::mock(WorkflowConditionTypeFake::class)
            ->shouldReceive('handle')
            ->andReturn(false)
            ->getMock();

        // Mock the action that will resolve the condition type
        $this->partialMock(GetWorkflowConditionTypeImplementationAction::class, function (MockInterface $mock) use ($eventCondition) {
            $mock->shouldReceive('handle')->andReturn($eventCondition);
        });

        // Get the next step
        $getNextStepAction = app(GetNextStepForWorkflowRunAction::class);
        $nextWorkflowRunStep = $getNextStepAction->handle($this->workflowRun);

        // Ensure that the prioritized step is returned
        $this->assertEquals($prioritizedWorkflowStep->id, $nextWorkflowRunStep->id);
    }
}
