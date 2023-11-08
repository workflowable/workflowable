<?php

namespace Workflowable\Workflowable\Tests\Unit\Managers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCreated;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessDispatched;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Jobs\WorkflowProcessRunnerJob;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcessTests;

class WorkflowableManagerTest extends TestCase
{
    use HasWorkflowProcessTests;

    public function test_that_we_can_trigger_an_event(): void
    {
        config()->set('workflowable.queue', 'test-queue');

        // Set up the fake queue and event
        Queue::fake();
        Event::fake();

        // Fire the workflow event
        $workflowRunCollection = $this->manager->triggerEvent(new WorkflowEventFake([
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

        $workflowRun = $this->manager->createWorkflowProcess($this->workflow, $workflowEventContract);
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

        $workflowRun = $this->manager->dispatchProcess($this->workflowProcess);

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
        $workflowRunCollection = $this->manager->triggerEvent($workflowEventContract);

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
        $workflowRunCollection = $this->manager->triggerEvent($workflowEventContract);

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
        $this->manager->triggerEvent($workflowEventContract);
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
        $this->manager->triggerEvent($workflowEventContract);

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

        $result = $this->manager->createInputToken($this->workflowProcess, 'test', 'test');
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

        $result = $this->manager->createOutputToken($this->workflowProcess, $this->fromWorkflowActivity, 'test', 'test');
        $this->assertInstanceOf(WorkflowProcessToken::class, $result);

        $this->assertDatabaseHas(WorkflowProcessToken::class, [
            'workflow_process_id' => $this->workflowProcess->id,
            'workflow_activity_id' => $this->fromWorkflowActivity->id,
            'key' => 'test',
            'value' => 'test',
        ]);
    }
}
