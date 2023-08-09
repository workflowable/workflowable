<?php

namespace Workflowable\Workflowable\Tests\Unit\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCancelled;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCreated;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessDispatched;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessPaused;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessResumed;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Jobs\WorkflowProcessRunnerJob;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcessTests;
use Workflowable\Workflowable\Traits\InteractsWithWorkflowProcesses;

class InteractsWithWorkflowRunsTest extends TestCase
{
    use InteractsWithWorkflowProcesses;
    use HasWorkflowProcessTests;

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
        Queue::assertPushed(WorkflowProcessRunnerJob::class, 1);
        Event::assertDispatched(WorkflowProcessCreated::class, 1);
        Event::assertDispatched(WorkflowProcessDispatched::class, 1);

        // Verify that the returned data looks correct
        $this->assertInstanceOf(Collection::class, $workflowRunCollection);
        $this->assertEquals(1, $workflowRunCollection->count());
        $this->assertInstanceOf(WorkflowProcess::class, $workflowRunCollection->first());

        // Assert it was correctly written to the database
        $this->assertDatabaseHas(WorkflowProcess::class, [
            'workflow_process_status_id' => WorkflowProcessStatusEnum::DISPATCHED,
            'workflow_id' => $this->workflow->id,
        ]);

        $this->assertDatabaseHas(WorkflowProcessToken::class, [
            'workflow_process_id' => $workflowRunCollection->first()->id,
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
        $this->assertInstanceOf(WorkflowProcess::class, $workflowRun);
        $this->assertEquals(WorkflowProcessStatusEnum::CREATED, $workflowRun->workflow_process_status_id);
        $this->assertEquals($this->workflow->id, $workflowRun->workflow_id);

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'workflow_process_status_id' => WorkflowProcessStatusEnum::CREATED,
            'workflow_id' => $this->workflow->id,
        ]);

        $this->assertDatabaseHas(WorkflowProcessToken::class, [
            'workflow_process_id' => $workflowRun->id,
            'key' => 'test',
            'value' => 'Test',
        ]);
    }

    public function test_that_we_can_dispatch_a_workflow_run()
    {
        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $workflowRun = $this->dispatchRun($this->workflowProcess);

        $this->assertInstanceOf(WorkflowProcess::class, $workflowRun);
        $this->assertEquals(WorkflowProcessStatusEnum::DISPATCHED, $workflowRun->workflow_process_status_id);
        $this->assertEquals($this->workflow->id, $workflowRun->workflow_id);

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'workflow_process_status_id' => WorkflowProcessStatusEnum::DISPATCHED,
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
        Queue::assertPushed(WorkflowProcessRunnerJob::class, 2);
        Event::assertDispatched(WorkflowProcessCreated::class, 2);
        Event::assertDispatched(WorkflowProcessDispatched::class, 2);

        // Verify that the returned data looks correct
        $this->assertInstanceOf(Collection::class, $workflowRunCollection);
        $this->assertEquals(2, $workflowRunCollection->count());
        $this->assertInstanceOf(WorkflowProcess::class, $workflowRunCollection->first());

        $workflows = collect([$this->workflow, $extraWorkflow]);
        foreach ($workflows as $workflow) {
            // Assert it was correctly written to the database
            $this->assertDatabaseHas(WorkflowProcess::class, [
                'workflow_process_status_id' => WorkflowProcessStatusEnum::DISPATCHED,
                'workflow_id' => $workflow->id,
            ]);

            $this->assertDatabaseHas(WorkflowProcessToken::class, [
                'workflow_process_id' => $workflowRunCollection->first()->id,
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

        $this->workflowProcess->delete();

        $this->workflow->update([
            'workflow_status_id' => WorkflowStatusEnum::ARCHIVED,
        ]);

        // Fire the workflow event
        $workflowRunCollection = $this->triggerEvent($workflowEventContract);

        // Assert that events and jobs were dispatched
        Queue::assertNotPushed(WorkflowProcessRunnerJob::class);
        Event::assertNotDispatched(WorkflowProcessCreated::class, 1);
        Event::assertNotDispatched(WorkflowProcessDispatched::class, 1);

        // Verify that the returned data looks correct
        $this->assertInstanceOf(Collection::class, $workflowRunCollection);
        $this->assertEquals(0, $workflowRunCollection->count());
        $this->assertEmpty($workflowRunCollection);

        // Assert it was correctly written to the database
        $this->assertDatabaseEmpty(WorkflowProcess::class);
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

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::PENDING,
        ]);

        // Call the action to cancel the workflow run
        $cancelledWorkflowRun = $this->cancelRun($this->workflowProcess);

        // Assert that the workflow run was cancelled
        $this->assertEquals(WorkflowProcessStatusEnum::CANCELLED, $cancelledWorkflowRun->workflow_process_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowProcessCancelled::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_cancelling_if_workflow_run_is_not_pending()
    {
        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::COMPLETED,
        ]);

        // Call the action to cancel the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not pending');

        $this->cancelRun($this->workflowProcess);
    }

    /** @test */
    public function it_should_pause_a_pending_workflow_run()
    {
        Event::fake();

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::PENDING,
        ]);

        // Call the action to pause the workflow run
        $pausedWorkflowRun = $this->pauseRun($this->workflowProcess);

        // Assert that the workflow run was paused
        $this->assertEquals(WorkflowProcessStatusEnum::PAUSED, $pausedWorkflowRun->workflow_process_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowProcessPaused::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_pausing_if_workflow_run_is_not_pending()
    {
        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::COMPLETED,
        ]);

        // Call the action to pause the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not pending');

        $this->pauseRun($this->workflowProcess);
    }

    /** @test */
    public function it_should_resume_a_paused_workflow_run()
    {
        Event::fake();

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::PAUSED,
        ]);

        // Call the action to resume the workflow run
        $resumedWorkflowRun = $this->resumeRun($this->workflowProcess);

        // Assert that the workflow run was resumed
        $this->assertEquals(WorkflowProcessStatusEnum::PENDING, $resumedWorkflowRun->workflow_process_status_id);

        // Assert that the event was dispatched
        Event::assertDispatched(WorkflowProcessResumed::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id;
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_resuming_if_workflow_run_is_not_paused()
    {
        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::CANCELLED,
        ]);

        // Call the action to resume the workflow run and expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Workflow run is not paused');

        $this->resumeRun($this->workflowProcess);
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
        Queue::assertPushed(WorkflowProcessRunnerJob::class, 1);
        Queue::assertPushedOn('test-queue', WorkflowProcessRunnerJob::class);
        Event::assertDispatched(WorkflowProcessCreated::class, 1);
        Event::assertDispatched(WorkflowProcessDispatched::class, 1);
    }

    public function test_that_we_can_create_an_input_token_for_our_workflow_run()
    {
        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $result = $this->createInputToken($this->workflowProcess, 'test', 'test');
        $this->assertInstanceOf(WorkflowProcessToken::class, $result);

        $this->assertDatabaseHas(WorkflowProcessToken::class, [
            'workflow_process_id' => $this->workflowProcess->id,
            'key' => 'test',
            'value' => 'test',
        ]);
    }

    public function test_that_we_can_create_an_output_token_for_our_workflow_run()
    {
        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        $result = $this->createOutputToken($this->workflowProcess, $this->fromWorkflowActivity, 'test', 'test');
        $this->assertInstanceOf(WorkflowProcessToken::class, $result);

        $this->assertDatabaseHas(WorkflowProcessToken::class, [
            'workflow_process_id' => $this->workflowProcess->id,
            'workflow_activity_id' => $this->fromWorkflowActivity->id,
            'key' => 'test',
            'value' => 'test',
        ]);
    }
}
