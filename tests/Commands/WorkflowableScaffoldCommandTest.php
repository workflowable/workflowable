<?php

namespace Workflowable\Workflowable\Tests\Commands;

use Workflowable\Workflowable\Actions\WorkflowEvents\RegisterWorkflowEventAction;
use Workflowable\Workflowable\Commands\WorkflowScaffoldCommand;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class WorkflowableScaffoldCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        WorkflowEvent::query()->delete();
        WorkflowActivityType::query()->delete();
        WorkflowConditionType::query()->delete();
    }

    public function test_that_a_registered_event_will_be_seeded_into_the_database(): void
    {

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->expectsOutput('Created new workflow event: '.(new WorkflowEventFake())->getName());
    }

    public function test_that_a_registered_activity_types_will_be_seeded_into_the_database(): void
    {
        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->expectsOutput('Created new workflow activity type: '.(new WorkflowActivityTypeFake())->getName());
    }

    public function test_that_a_registered_condition_will_be_seeded_into_the_database(): void
    {
        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->expectsOutput('Created new workflow condition type: '.(new WorkflowConditionTypeFake())->getName());
    }

    public function test_that_a_already_registered_workflow_event_wont_be_seeded_twice(): void
    {
        RegisterWorkflowEventAction::make()->handle(new WorkflowEventFake());

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput('Created new workflow event: '.(new WorkflowEventFake())->getName());
    }

    public function test_that_a_already_registered_workflow_action_wont_be_seeded_twice(): void
    {
        WorkflowActivityType::factory()->withContract(new WorkflowActivityTypeFake())->create([
            'created_at' => now()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput('Created new workflow activity type: '.(new WorkflowActivityTypeFake())->getName());
    }

    public function test_that_a_already_registered_workflow_condition_wont_be_seeded_twice(): void
    {
        WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create([
            'created_at' => now()->subHour()->format('Y-m-d H:i:s'),
        ]);

        $this->artisan(WorkflowScaffoldCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput('Created new workflow condition type: '.(new WorkflowConditionTypeFake())->getName());
    }
}
