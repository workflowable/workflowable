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
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunStatus;
use Workflowable\Workflowable\Models\WorkflowRunToken;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowRunTests;
use Workflowable\Workflowable\Traits\InteractsWithWorkflowRuns;

class InteractsWithWorkflowRunsTest extends TestCase
{
    use InteractsWithWorkflowRuns;
    use HasWorkflowRunTests;

    public function test_that_we_can_trigger_an_event(): void
    {
        config()->set('workflowable.queue', 'test-queue');

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        // Fire the workflow event
        $workflowRunCollection = $this->triggerEvent(new WorkflowEventFake([
            'test' => 'Test',
        ]));

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
            'workflow_id' => $this->workflow->id,
        ]);

        $this->assertDatabaseHas(WorkflowRunToken::class, [
            'workflow_run_id' => $workflowRunCollection->first()->id,
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

        $workflowRun = $this->createWorkflowRun($this->workflow, $workflowEventContract);
        $this->assertInstanceOf(WorkflowRun::class, $workflowRun);
        $this->assertEquals(WorkflowRunStatus::CREATED, $workflowRun->workflow_run_status_id);
        $this->assertEquals($this->workflow->id, $workflowRun->workflow_id);

        $this->assertDatabaseHas(WorkflowRun::class, [
            'workflow_run_status_id' => WorkflowRunStatus::CREATED,
            'workflow_id' => $this->workflow->id,
        ]);

        $this->assertDatabaseHas(WorkflowRunToken::class, [
            'workflow_run_id' => $workflowRun->id,
            'key' => 'test',
            'value' => 'Test',
        ]);
    }

    public function test_that_we_can_dispatch_a_workflow_run()
    {
        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $workflowRun = $this->dispatchRun($this->workflowRun);

        $this->assertInstanceOf(WorkflowRun::class, $workflowRun);
        $this->assertEquals(WorkflowRunStatus::DISPATCHED, $workflowRun->workflow_run_status_id);
        $this->assertEquals($this->workflow->id, $workflowRun->workflow_id);

        $this->assertDatabaseHas(WorkflowRun::class, [
            'workflow_run_status_id' => WorkflowRunStatus::DISPATCHED,
            'workflow_id' => $this->workflow->id,
        ]);
    }

    public function test_that_we_can_fire_off_multiple_workflows_for_the_same_event()
    {
        config()->set('workflowable.queue', 'test-queue');

        $workflowEventContract = new WorkflowEventFake([
            'test' => 'Test',
        ]);

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $extraWorkflow = Workflow::factory()->withWorkflowEvent($this->workflowEvent)->create();

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

        $workflows = collect([$this->workflow, $extraWorkflow]);
        foreach ($workflows as $workflow) {
            // Assert it was correctly written to the database
            $this->assertDatabaseHas(WorkflowRun::class, [
                'workflow_run_status_id' => WorkflowRunStatus::DISPATCHED,
                'workflow_id' => $workflow->id,
            ]);

            $this->assertDatabaseHas(WorkflowRunToken::class, [
                'workflow_run_id' => $workflowRunCollection->first()->id,
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

        $this->workflowRun->delete();

        $this->workflow->update([
            'workflow_status_id' => WorkflowStatus::ARCHIVED,
        ]);

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

        $this->expectException(WorkflowEventException::class);
        $this->expectExceptionMessage(WorkflowEventException::invalidWorkflowEventParameters()->getMessage());
        $this->triggerEvent($workflowEventContract);
    }

    /** @test */
    public function it_should_cancel_a_pending_workflow_run()
    {
        Event::fake();

        $this->workflowRun->update([
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
        ]);

        // Call the action to cancel the workflow run
        $cancelledWorkflowRun = $this->cancelRun($this->workflowRun);

        // Assert that the workflow run was cancelled
        $this->assertEquals(WorkflowRunStatus::CANCELLED, $cancelledWorkflowRun->workflow_run_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowRunCancelled::class, function ($event) {
            return $event->workflowRun->id === $this->workflowRun->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_cancelling_if_workflow_run_is_not_pending()
    {
        $this->workflowRun->update([
            'workflow_run_status_id' => WorkflowRunStatus::COMPLETED,
        ]);

        // Call the action to cancel the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not pending');

        $this->cancelRun($this->workflowRun);
    }

    /** @test */
    public function it_should_pause_a_pending_workflow_run()
    {
        Event::fake();

        $this->workflowRun->update([
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
        ]);

        // Call the action to pause the workflow run
        $pausedWorkflowRun = $this->pauseRun($this->workflowRun);

        // Assert that the workflow run was paused
        $this->assertEquals(WorkflowRunStatus::PAUSED, $pausedWorkflowRun->workflow_run_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowRunPaused::class, function ($event) {
            return $event->workflowRun->id === $this->workflowRun->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_pausing_if_workflow_run_is_not_pending()
    {
        $this->workflowRun->update([
            'workflow_run_status_id' => WorkflowRunStatus::COMPLETED,
        ]);

        // Call the action to pause the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not pending');

        $this->pauseRun($this->workflowRun);
    }

    /** @test */
    public function it_should_resume_a_paused_workflow_run()
    {
        Event::fake();

        $this->workflowRun->update([
            'workflow_run_status_id' => WorkflowRunStatus::PAUSED,
        ]);

        // Call the action to resume the workflow run
        $resumedWorkflowRun = $this->resumeRun($this->workflowRun);

        // Assert that the workflow run was resumed
        $this->assertEquals(WorkflowRunStatus::PENDING, $resumedWorkflowRun->workflow_run_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowRunResumed::class, function ($event) {
            return $event->workflowRun->id === $this->workflowRun->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_resuming_if_workflow_run_is_not_paused()
    {
        $this->workflowRun->update([
            'workflow_run_status_id' => WorkflowRunStatus::CANCELLED,
        ]);

        // Call the action to resume the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not paused');

        $this->resumeRun($this->workflowRun);
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

        // Fire the workflow event
        $this->triggerEvent($workflowEventContract);

        // Assert that events and jobs were dispatched
        Queue::assertPushed(WorkflowRunnerJob::class, 1);
        Queue::assertPushedOn('test-queue', WorkflowRunnerJob::class);
        Event::assertDispatched(WorkflowRunCreated::class, 1);
        Event::assertDispatched(WorkflowRunDispatched::class, 1);
    }

    public function test_that_we_can_create_an_input_parameter_for_our_workflow_run()
    {
        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $result = $this->createInputParameter($this->workflowRun, 'test', 'test');
        $this->assertInstanceOf(WorkflowRunToken::class, $result);

        $this->assertDatabaseHas(WorkflowRunToken::class, [
            'workflow_run_id' => $this->workflowRun->id,
            'key' => 'test',
            'value' => 'test',
        ]);
    }

    public function test_that_we_can_create_an_output_parameter_for_our_workflow_run()
    {
        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $result = $this->createOutputParameter($this->workflowRun, $this->fromWorkflowActivity, 'test', 'test');
        $this->assertInstanceOf(WorkflowRunToken::class, $result);

        $this->assertDatabaseHas(WorkflowRunToken::class, [
            'workflow_run_id' => $this->workflowRun->id,
            'workflow_activity_id' => $this->fromWorkflowActivity->id,
            'key' => 'test',
            'value' => 'test',
        ]);
    }
}
