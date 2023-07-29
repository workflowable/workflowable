<?php

namespace Workflowable\Workflowable\Tests\Unit\Commands;

use Mockery\MockInterface;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\GetWorkflowActivityTypeImplementationAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflowable\Commands\VerifyIntegrityOfWorkflowEventCommand;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class VerifyIntegrityOfWorkflowEventCommandTest extends TestCase
{
    public function test_that_it_logs_an_error_when_workflow_condition_type_requires_keys_not_in_workflow_event()
    {
        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeEventConstrainedFake::class,
        ]);

        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);

        config()->set('workflowable.cache_keys', [
            'workflow_events' => 'workflowable:workflow_events',
            'workflow_condition_types' => 'workflowable:workflow_condition_types',
            'workflow_activity_types' => 'workflowable:workflow_activity_types',
        ]);

        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeEventConstrainedFake())->create();

        $eventCondition = \Mockery::mock(WorkflowConditionTypeEventConstrainedFake::class)
            ->shouldReceive('getRequiredWorkflowEventParameterKeys')
            ->andReturn([
                'test' => 'required',
                'test2' => 'required',
            ])->getMock();

        $this->partialMock(GetWorkflowConditionTypeImplementationAction::class, function (MockInterface $mock) use ($eventCondition) {
            $mock->shouldReceive('handle')->andReturn($eventCondition);
        });

        $event = new WorkflowEventFake();
        $workflowConditionType = new WorkflowConditionTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertFailed()
            ->expectsOutput("Workflow condition type {$workflowConditionType->getAlias()} on workflow event {$event->getAlias()} is not verified.");
    }

    public function test_that_it_logs_no_error_when_workflow_condition_type_requires_keys_in_workflow_event()
    {
        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeEventConstrainedFake::class,
        ]);

        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);

        config()->set('workflowable.cache_keys', [
            'workflow_events' => 'workflowable:workflow_events',
            'workflow_condition_types' => 'workflowable:workflow_condition_types',
            'workflow_activity_types' => 'workflowable:workflow_activity_types',
        ]);

        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeEventConstrainedFake())->create();

        $event = new WorkflowEventFake();
        $workflowConditionType = new WorkflowConditionTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput("Workflow condition type {$workflowConditionType->getAlias()} on workflow event {$event->getAlias()} is not verified.");
    }

    public function test_that_it_logs_an_error_when_workflow_activity_type_requires_keys_not_in_workflow_event()
    {
        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeEventConstrainedFake::class,
        ]);

        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);

        config()->set('workflowable.cache_keys', [
            'workflow_events' => 'workflowable:workflow_events',
            'workflow_condition_types' => 'workflowable:workflow_condition_types',
            'workflow_activity_types' => 'workflowable:workflow_activity_types',
        ]);

        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        WorkflowActivityType::factory()->withContract(new WorkflowActivityTypeEventConstrainedFake())->create();

        $eventActivityType = \Mockery::mock(WorkflowActivityTypeEventConstrainedFake::class)
            ->shouldReceive('getRequiredWorkflowEventParameterKeys')
            ->andReturn([
                'test' => 'required',
                'test2' => 'required',
            ])->getMock();

        $this->partialMock(GetWorkflowActivityTypeImplementationAction::class, function (MockInterface $mock) use ($eventActivityType) {
            $mock->shouldReceive('handle')->andReturn($eventActivityType);
        });

        $event = new WorkflowEventFake();
        $workflowActivityType = new WorkflowActivityTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertFailed()
            ->expectsOutput("Workflow activity type {$workflowActivityType->getAlias()} on workflow event {$event->getAlias()} is not verified.");
    }

    public function test_that_it_logs_no_error_when_workflow_activity_type_requires_keys_in_workflow_event()
    {
        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeEventConstrainedFake::class,
        ]);

        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);

        config()->set('workflowable.cache_keys', [
            'workflow_events' => 'workflowable:workflow_events',
            'workflow_condition_types' => 'workflowable:workflow_condition_types',
            'workflow_activity_types' => 'workflowable:workflow_activity_types',
        ]);

        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        WorkflowActivityType::factory()->withContract(new WorkflowActivityTypeEventConstrainedFake())->create();

        $event = new WorkflowEventFake();
        $workflowActivityType = new WorkflowActivityTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput("Workflow activity type {$workflowActivityType->getAlias()} on workflow event {$event->getAlias()} is not verified.");
    }
}
