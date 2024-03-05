<?php

namespace Workflowable\Workflowable\Tests\Actions\WorkflowProcesses;

use Mockery\MockInterface;
use Workflowable\Workflowable\Actions\WorkflowProcesses\GetNextActivityForWorkflowProcessAction;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

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
        $workflowConditionType = WorkflowConditionType::query()->where('class_name', WorkflowConditionTypeFake::class)->firstOrFail();

        WorkflowCondition::factory()
            ->withWorkflowTransition($this->workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->withParameters([
                'foo' => 'bar',
            ])
            ->create();

        $this->partialMock(WorkflowConditionTypeFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->andReturn(false);
        });

        // Get the next activity
        $nextWorkflowRunActivity = GetNextActivityForWorkflowProcessAction::make()->handle($this->workflowProcess);

        // Ensure that the prioritized activity is returned
        $this->assertEquals($prioritizedWorkflowActivity->id, $nextWorkflowRunActivity->id);
    }
}
