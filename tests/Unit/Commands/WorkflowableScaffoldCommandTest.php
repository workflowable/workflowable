<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Commands;

use Workflowable\WorkflowEngine\Commands\WorkflowScaffoldCommand;
use Workflowable\WorkflowEngine\Models\WorkflowConditionType;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStepType;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\WorkflowEngine\Tests\TestCase;

class WorkflowableScaffoldCommandTest extends TestCase
{
    public function test_that_a_registered_event_will_be_seeded_into_the_database(): void
    {
        config()->set('workflow-engine.workflow_events', [
            WorkflowEventFake::class,
        ]);

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->expectsOutput('Created new workflow event: '.(new WorkflowEventFake())->getName());
    }

    public function test_that_a_registered_step_types_will_be_seeded_into_the_database(): void
    {
        config()->set('workflow-engine.workflow_step_types', [
            WorkflowStepTypeFake::class,
        ]);

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->expectsOutput('Created new workflow step type: '.(new WorkflowStepTypeFake())->getName());
    }

    public function test_that_a_registered_condition_will_be_seeded_into_the_database(): void
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->expectsOutput('Created new workflow condition type: '.(new WorkflowConditionTypeFake())->getName());
    }

    public function test_that_a_already_registered_workflow_event_wont_be_seeded_twice(): void
    {
        config()->set('workflow-engine.workflow_events', [
            WorkflowEventFake::class,
        ]);

        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create([
            'created_at' => now()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput('Created new workflow event: '.(new WorkflowEventFake())->getName());
    }

    public function test_that_a_already_registered_workflow_action_wont_be_seeded_twice(): void
    {
        config()->set('workflow-engine.workflow_step_types', [
            WorkflowStepTypeFake::class,
        ]);

        WorkflowStepType::factory()->withContract(new WorkflowStepTypeFake())->create([
            'created_at' => now()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput('Created new workflow step type: '.(new WorkflowStepTypeFake())->getName());
    }

    public function test_that_a_already_registered_workflow_condition_wont_be_seeded_twice(): void
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create([
            'created_at' => now()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput('Created new workflow condition type: '.(new WorkflowConditionTypeFake())->getName());
    }
}
