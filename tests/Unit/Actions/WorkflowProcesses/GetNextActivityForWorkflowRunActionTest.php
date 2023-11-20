<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowProcesses;

use Mockery\MockInterface;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflowable\Actions\WorkflowProcesses\GetNextActivityForWorkflowProcessAction;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcess;

class GetNextActivityForWorkflowRunActionTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_get_the_next_activity_for_a_workflow_run(): void
    {
        $nextWorkflowProcessActivity = GetNextActivityForWorkflowProcessAction::make()->handle($this->workflowProcess);

        $this->assertEquals($this->toWorkflowActivity->id, $nextWorkflowProcessActivity->id);
    }

    public function test_that_we_will_prioritize_evaluating_transitions_that_have_a_lower_ordinal_value(): void
    {
        $this->workflowTransition->update(['ordinal' => 2]);

        $prioritizedWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflow)
            ->create();

        WorkflowTransition::factory()
            ->withWorkflow($this->workflow)
            ->withFromWorkflowActivity($this->fromWorkflowActivity)
            ->withToWorkflowActivity($prioritizedWorkflowActivity)
            ->create([
                'ordinal' => 1,
            ]);

        $nextWorkflowProcessActivity = GetNextActivityForWorkflowProcessAction::make()->handle($this->workflowProcess);

        $this->assertEquals($prioritizedWorkflowActivity->id, $nextWorkflowProcessActivity->id);
    }

    public function test_that_we_will_check_conditions_before_deciding_if_a_transition_may_be_performed(): void
    {
        // Add the condition type to config so that it can be resolved later
        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        // Create the activity that we want to be prioritized
        $prioritizedWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflow)
            ->create();

        // Ensure this transition will be processed first
        $this->workflowTransition->update(['ordinal' => 1]);

        // Ensure that it has a higher ordinal value than the other transition so that it will not be prioritized
        WorkflowTransition::factory()
            ->withWorkflow($this->workflow)
            ->withFromWorkflowActivity($this->fromWorkflowActivity)
            ->withToWorkflowActivity($prioritizedWorkflowActivity)
            ->create([
                'ordinal' => 2,
            ]);

        // Build out the condition that will be evaluated
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        WorkflowCondition::factory()
            ->withWorkflowTransition($this->workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->withParameters([
                'foo' => 'bar',
            ])
            ->create();

        // Mock the condition type so that it will return false
        $eventCondition = \Mockery::mock(WorkflowConditionTypeFake::class)
            ->shouldReceive('handle')
            ->andReturn(false)
            ->getMock();

        GetWorkflowConditionTypeImplementationAction::fake(function (MockInterface $mock) use ($eventCondition) {
            $mock->shouldReceive('handle')->andReturn($eventCondition);
        });

        // Get the next activity
        $nextWorkflowRunActivity = GetNextActivityForWorkflowProcessAction::make()->handle($this->workflowProcess);

        // Ensure that the prioritized activity is returned
        $this->assertEquals($prioritizedWorkflowActivity->id, $nextWorkflowRunActivity->id);
    }
}
