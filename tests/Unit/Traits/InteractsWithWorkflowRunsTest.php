<?php

namespace Workflowable\Workflowable\Tests\Unit\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunCancelled;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunCreated;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunDispatched;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunPaused;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunResumed;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Jobs\WorkflowRunnerJob;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowableParameter;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunStatus;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Traits\InteractsWithWorkflowRuns;

class InteractsWithWorkflowRunsTest extends TestCase
{
    use InteractsWithWorkflowRuns;

    public function test_that_we_can_trigger_an_event(): void
    {
        $workflowEventContract = new WorkflowEventFake([
            'test' => 'Test',
        ]);

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        // Set up the data
        $workflowEvent = WorkflowEvent::factory()->withContract($workflowEventContract)->create();
        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Fire the workflow event
        $workflowRunCollection = $this->triggerEvent($workflowEventContract);

        // Assert that events and jobs were dispatched
        Queue::assertPushed(WorkflowRunnerJob::class, 1);
        Event::assertDispatched(WorkflowRunCreated::class, 1);
        Event::assertDispatched(WorkflowRunDispatched::class, 1);

        // Verify that the returned data looks correct
        $this->assertInstanceOf(Collection::class, $workflowRunCollection);
        $this->assertEquals(1, $workflowRunCollection->count());
        $this->assertInstanceOf(WorkflowRun::class, $workflowRunCollection->first());

        // Assert it was correctly written to the database
        $this->assertDatabaseHas(WorkflowRun::class, [
            'workflow_run_status_id' => WorkflowRunStatus::DISPATCHED,
            'workflow_id' => $workflow->id,
        ]);

        $this->assertDatabaseHas(WorkflowableParameter::class, [
            'parameterizable_id' => $workflowRunCollection->first()->id,
            'parameterizable_type' => WorkflowRun::class,
            'key' => 'test',
            'value' => 'Test',
        ]);
    }

    public function test_that_we_can_create_a_workflow_run()
    {
        $workflowEventContract = new WorkflowEventFake([
            'test' => 'Test',
        ]);

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        // Set up the data
        $workflowEvent = WorkflowEvent::factory()->withContract($workflowEventContract)->create();
        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        $workflowRun = $this->createWorkflowRun($workflow, $workflowEventContract);
        $this->assertInstanceOf(WorkflowRun::class, $workflowRun);
        $this->assertEquals(WorkflowRunStatus::CREATED, $workflowRun->workflow_run_status_id);
        $this->assertEquals($workflow->id, $workflowRun->workflow_id);

        $this->assertDatabaseHas(WorkflowRun::class, [
            'workflow_run_status_id' => WorkflowRunStatus::CREATED,
            'workflow_id' => $workflow->id,
        ]);

        $this->assertDatabaseHas(WorkflowableParameter::class, [
            'parameterizable_id' => $workflowRun->id,
            'parameterizable_type' => WorkflowRun::class,
            'key' => 'test',
            'value' => 'Test',
        ]);
    }

    public function test_that_we_can_dispatch_a_workflow_run()
    {
        $workflowEventContract = new WorkflowEventFake([
            'test' => 'Test',
        ]);

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        // Set up the data
        $workflowEvent = WorkflowEvent::factory()->withContract($workflowEventContract)->create();
        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        $workflowRun = WorkflowRun::factory()
            ->withWorkflow($workflow)
            ->withWorkflowRunStatus(WorkflowRunStatus::CREATED)
            ->create();

        $workflowRun = $this->dispatchRun($workflowRun);

        $this->assertInstanceOf(WorkflowRun::class, $workflowRun);
        $this->assertEquals(WorkflowRunStatus::DISPATCHED, $workflowRun->workflow_run_status_id);
        $this->assertEquals($workflow->id, $workflowRun->workflow_id);

        $this->assertDatabaseHas(WorkflowRun::class, [
            'workflow_run_status_id' => WorkflowRunStatus::DISPATCHED,
            'workflow_id' => $workflow->id,
        ]);
    }

    public function test_that_we_can_fire_off_multiple_workflows_for_the_same_event()
    {
        $workflowEventContract = new WorkflowEventFake([
            'test' => 'Test',
        ]);

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        // Set up the data
        $workflowEvent = WorkflowEvent::factory()->withContract($workflowEventContract)->create();
        $workflows = Workflow::factory()->withWorkflowEvent($workflowEvent)->count(2)->create();

        // Fire the workflow event
        $workflowRunCollection = $this->triggerEvent($workflowEventContract);

        // Assert that events and jobs were dispatched
        Queue::assertPushed(WorkflowRunnerJob::class, 2);
        Event::assertDispatched(WorkflowRunCreated::class, 2);
        Event::assertDispatched(WorkflowRunDispatched::class, 2);

        // Verify that the returned data looks correct
        $this->assertInstanceOf(Collection::class, $workflowRunCollection);
        $this->assertEquals(2, $workflowRunCollection->count());
        $this->assertInstanceOf(WorkflowRun::class, $workflowRunCollection->first());

        foreach ($workflows as $workflow) {
            // Assert it was correctly written to the database
            $this->assertDatabaseHas(WorkflowRun::class, [
                'workflow_run_status_id' => WorkflowRunStatus::DISPATCHED,
                'workflow_id' => $workflow->id,
            ]);

            $this->assertDatabaseHas(WorkflowableParameter::class, [
                'parameterizable_id' => $workflowRunCollection->first()->id,
                'parameterizable_type' => WorkflowRun::class,
                'key' => 'test',
                'value' => 'Test',
            ]);
        }
    }

    public function test_that_workflows_not_in_active_state_will_not_be_triggered()
    {
        $workflowEventContract = new WorkflowEventFake([
            'test' => 'test',
        ]);

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        // Set up the data
        $workflowEvent = WorkflowEvent::factory()->withContract($workflowEventContract)->create();
        Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ARCHIVED)
            ->create();

        // Fire the workflow event
        $workflowRunCollection = $this->triggerEvent($workflowEventContract);

        // Assert that events and jobs were dispatched
        Queue::assertNotPushed(WorkflowRunnerJob::class);
        Event::assertNotDispatched(WorkflowRunCreated::class, 1);
        Event::assertNotDispatched(WorkflowRunDispatched::class, 1);

        // Verify that the returned data looks correct
        $this->assertInstanceOf(Collection::class, $workflowRunCollection);
        $this->assertEquals(0, $workflowRunCollection->count());
        $this->assertEmpty($workflowRunCollection);

        // Assert it was correctly written to the database
        $this->assertDatabaseEmpty(WorkflowRun::class);
    }

    public function test_that_invalid_workflow_event_parameters_will_throw_exception()
    {
        $workflowEventContract = new WorkflowEventFake([
            'test' => null,
        ]);
        // Set up the data
        $workflowEvent = WorkflowEvent::factory()->withContract($workflowEventContract)->create();
        Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        $this->expectException(WorkflowEventException::class);
        $this->expectExceptionMessage(WorkflowEventException::invalidWorkflowEventParameters()->getMessage());
        $this->triggerEvent($workflowEventContract);
    }

    /** @test */
    public function it_should_cancel_a_pending_workflow_run()
    {
        Event::fake();

        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
        ]);

        // Call the action to cancel the workflow run
        $cancelledWorkflowRun = $this->cancelRun($workflowRun);

        // Assert that the workflow run was cancelled
        $this->assertEquals(WorkflowRunStatus::CANCELLED, $cancelledWorkflowRun->workflow_run_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowRunCancelled::class, function ($event) use ($workflowRun) {
            return $event->workflowRun->id === $workflowRun->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_cancelling_if_workflow_run_is_not_pending()
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::COMPLETED,
        ]);

        // Call the action to cancel the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not pending');

        $this->cancelRun($workflowRun);
    }

    /** @test */
    public function it_should_pause_a_pending_workflow_run()
    {
        Event::fake();

        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
        ]);

        // Call the action to pause the workflow run
        $pausedWorkflowRun = $this->pauseRun($workflowRun);

        // Assert that the workflow run was paused
        $this->assertEquals(WorkflowRunStatus::PAUSED, $pausedWorkflowRun->workflow_run_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowRunPaused::class, function ($event) use ($workflowRun) {
            return $event->workflowRun->id === $workflowRun->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_pausing_if_workflow_run_is_not_pending()
    {
        // Create a new completed workflow run
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::COMPLETED,
        ]);

        // Call the action to pause the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not pending');

        $this->pauseRun($workflowRun);
    }

    /** @test */
    public function it_should_resume_a_paused_workflow_run()
    {
        Event::fake();

        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake(['test' => []]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::PAUSED,
        ]);

        // Call the action to resume the workflow run
        $resumedWorkflowRun = $this->resumeRun($workflowRun);

        // Assert that the workflow run was resumed
        $this->assertEquals(WorkflowRunStatus::PENDING, $resumedWorkflowRun->workflow_run_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowRunResumed::class, function ($event) use ($workflowRun) {
            return $event->workflowRun->id === $workflowRun->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_resuming_if_workflow_run_is_not_paused()
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::CANCELLED,
        ]);

        // Call the action to resume the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not paused');

        $this->resumeRun($workflowRun);
    }

    public function test_that_when_triggering_an_event_we_will_dispatch_the_workflow_run_on_the_workflow_event_queue()
    {
        config()->set('workflowable.queue', 'test-queue');

        $workflowEventContract = new WorkflowEventFake([
            'test' => 'Test',
        ]);

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        // Set up the data
        $workflowEvent = WorkflowEvent::factory()->withContract($workflowEventContract)->create();
        Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        // Fire the workflow event
        $this->triggerEvent($workflowEventContract);

        // Assert that events and jobs were dispatched
        Queue::assertPushed(WorkflowRunnerJob::class, 1);
        Queue::assertPushedOn('test-queue', WorkflowRunnerJob::class);
        Event::assertDispatched(WorkflowRunCreated::class, 1);
        Event::assertDispatched(WorkflowRunDispatched::class, 1);
    }
}
