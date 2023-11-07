<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowActivities;

use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Workflowable\Workflowable\Actions\WorkflowActivities\ExecuteWorkflowActivityAction;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\GetWorkflowActivityTypeImplementationAction;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityCompleted;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityFailed;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityStarted;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcessTests;

class ExecuteWorkflowActivityActionTest extends TestCase
{
    use HasWorkflowProcessTests;

    public function test_that_upon_starting_the_execution_of_a_workflow_activity_we_will_dispatch_the_started_event()
    {
        Event::fake();

        ExecuteWorkflowActivityAction::make()->handle($this->workflowProcess, $this->fromWorkflowActivity);

        Event::assertDispatched(WorkflowActivityStarted::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id
                && $this->fromWorkflowActivity->id === $event->workflowActivity->id;
        });
    }

    public function test_that_upon_successfully_completing_execution_we_will_dispatch_the_completed_event()
    {
        Event::fake();

        ExecuteWorkflowActivityAction::make()->handle($this->workflowProcess, $this->fromWorkflowActivity);

        Event::assertDispatched(WorkflowActivityCompleted::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id
                && $this->fromWorkflowActivity->id === $event->workflowActivity->id;
        });
    }

    public function test_that_upon_failing_to_execute_the_activity_we_will_dispatch_the_failed_event()
    {
        $mockedActivityType = $this->partialMock(WorkflowActivityTypeFake::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new \Exception());
        });

        GetWorkflowActivityTypeImplementationAction::fake(function (MockInterface $mock) use ($mockedActivityType) {
            $mock->shouldReceive('handle')
                ->once()
                ->andReturn($mockedActivityType);
        });

        Event::fake();

        $this->expectException(\Exception::class);
        ExecuteWorkflowActivityAction::make()->handle($this->workflowProcess, $this->fromWorkflowActivity);

        Event::assertDispatched(WorkflowActivityFailed::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id
                && $this->fromWorkflowActivity->id === $event->workflowActivity->id;
        });
    }
}
