<?php

namespace Workflowable\Workflowable\Tests\Commands;

use Mockery\MockInterface;
use Workflowable\Workflowable\Commands\VerifyIntegrityOfWorkflowEventCommand;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class VerifyIntegrityOfWorkflowEventCommandTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_it_logs_an_error_when_workflow_condition_type_requires_keys_not_in_workflow_event()
    {
        $this->partialMock(WorkflowConditionTypeEventConstrainedFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('getRequiredWorkflowEventTokenKeys')
                ->andReturn([
                    'test' => 'required',
                    'test2' => 'required',
                ]);
        });

        $event = new WorkflowEventFake();
        $workflowConditionType = new WorkflowConditionTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertFailed()
            ->expectsOutput("Workflow condition type {$workflowConditionType->getName()} on workflow event {$event->getName()} is not verified.");
    }

    public function test_that_it_logs_no_error_when_workflow_condition_type_requires_keys_in_workflow_event()
    {
        $event = new WorkflowEventFake();
        $workflowConditionType = new WorkflowConditionTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput("Workflow condition type {$workflowConditionType->getName()} on workflow event {$event->getName()} is not verified.");
    }

    public function test_that_it_logs_an_error_when_workflow_activity_type_requires_keys_not_in_workflow_event()
    {
        $this->partialMock(WorkflowActivityTypeEventConstrainedFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('getRequiredWorkflowEventTokenKeys')
                ->andReturn([
                    'test' => 'required',
                    'test2' => 'required',
                ]);
        });

        $event = new WorkflowEventFake();
        $workflowActivityType = new WorkflowActivityTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertFailed()
            ->expectsOutput("Workflow activity type {$workflowActivityType->getName()} on workflow event {$event->getName()} is not verified.");
    }

    public function test_that_it_logs_no_error_when_workflow_activity_type_requires_keys_in_workflow_event()
    {
        $event = new WorkflowEventFake();
        $workflowActivityType = new WorkflowActivityTypeEventConstrainedFake();
        $this->artisan(VerifyIntegrityOfWorkflowEventCommand::class)
            ->assertSuccessful()
            ->doesntExpectOutput("Workflow activity type {$workflowActivityType->getName()} on workflow event {$event->getName()} is not verified.");
    }
}
