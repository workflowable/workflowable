<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowEvents;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflow\Actions\WorkflowEvents\DispatchWorkflowEventAction;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCreated;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunDispatched;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Jobs\WorkflowRunnerJob;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunParameter;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\TestCase;

class DispatchWorkflowEventActionTest extends TestCase
{
    use DatabaseTransactions;

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
        $workflowRunCollection = app(DispatchWorkflowEventAction::class)->handle($workflowEventContract);

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

        $this->assertDatabaseHas(WorkflowRunParameter::class, [
            'workflow_run_id' => $workflowRunCollection->first()->id,
            'name' => 'test',
            'value' => 'Test',
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
        $workflowRunCollection = app(DispatchWorkflowEventAction::class)->handle($workflowEventContract);

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

            $this->assertDatabaseHas(WorkflowRunParameter::class, [
                'workflow_run_id' => $workflowRunCollection->first()->id,
                'name' => 'test',
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
        $workflowRunCollection = app(DispatchWorkflowEventAction::class)->handle($workflowEventContract);

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
        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->create();

        $this->expectException(WorkflowEventException::class);
        $this->expectExceptionMessage(WorkflowEventException::invalidWorkflowEventParameters()->getMessage());
        app(DispatchWorkflowEventAction::class)->handle($workflowEventContract);
    }
}
