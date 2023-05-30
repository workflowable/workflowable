<?php

namespace Workflowable\Workflow\Tests\Unit\Commands;

use Mockery\Mock;
use Mockery\MockInterface;
use Workflowable\Workflow\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflow\Commands\VerifyIntegrityOfWorkflowEventCommand;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class VerifyIntegrityOfWorkflowEventCommandTest extends TestCase
{
    public function test_that_it_logs_an_error_when_workflow_condition_type_requires_keys_not_in_workflow_event()
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeEventConstrainedFake::class,
        ]);

        config()->set('workflow-engine.workflow_events', [
            WorkflowEventFake::class,
        ]);

        config()->set('workflow-engine.cache_keys',  [
            'workflow_events' => 'workflowable:workflow_events',
            'workflow_condition_types' => 'workflowable:workflow_condition_types',
            'workflow_step_types' => 'workflowable:workflow_step_types',
        ]);

        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeEventConstrainedFake())->create();

        $eventCondition = \Mockery::mock(WorkflowConditionTypeEventConstrainedFake::class)
            ->shouldReceive('getRequiredWorkflowEventKeys')
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
            ->assertSuccessful()
            ->expectsOutput("Workflow condition type {$workflowConditionType->getAlias()} on workflow event {$event->getAlias()} is not verified.");
    }

    public function test_that_it_logs_no_error_when_workflow_condition_type_requires_keys_in_workflow_event()
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeEventConstrainedFake::class,
        ]);

        config()->set('workflow-engine.workflow_events', [
            WorkflowEventFake::class,
        ]);

        config()->set('workflow-engine.cache_keys',  [
            'workflow_events' => 'workflowable:workflow_events',
            'workflow_condition_types' => 'workflowable:workflow_condition_types',
            'workflow_step_types' => 'workflowable:workflow_step_types',
        ]);

        WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeEventConstrainedFake())->create();

        $event = new WorkflowEventFake();
        $workflowConditionType = new WorkflowConditionTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput("Workflow condition type {$workflowConditionType->getAlias()} on workflow event {$event->getAlias()} is not verified.");
    }

    public function test_that_it_logs_an_error_when_workflow_step_type_requires_keys_not_in_workflow_event()
    {
        $this->markTestSkipped('Not implemented yet.');
    }

    public function test_that_it_logs_no_error_when_workflow_step_type_requires_keys_in_workflow_event()
    {
        $this->markTestSkipped('Not implemented yet.');
    }
}
