<?php

namespace Workflowable\Workflow\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCompleted;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunFailed;
use Workflowable\Workflow\Jobs\WorkflowRunnerJob;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Tests\TestCase;
use Workflowable\Workflow\Tests\Traits\HasWorkflowRunTests;

class WorkflowRunnerJobTest extends TestCase
{
    use HasWorkflowRunTests;

    public function test_that_we_can_mark_a_workflow_run_as_complete(): void
    {
        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowRunnerJob($this->workflowRun);
        Event::fake();
        $job->markRunComplete();

        $this->assertDatabaseHas(WorkflowRun::class, [
            'id' => $this->workflowRun->id,
            'workflow_run_status_id' => WorkflowRunStatus::COMPLETED,
            'completed_at' => now()->startOfSecond(),
        ]);

        Event::assertDispatched(WorkflowRunCompleted::class, function ($event) {
            return $event->workflowRun->id === $this->workflowRun->id;
        });
    }

    public function test_that_we_can_mark_a_workflow_run_as_failed(): void
    {
        Event::fake();
        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowRunnerJob($this->workflowRun);

        $job->failed(new \Exception('Test exception'));

        Event::assertDispatched(WorkflowRunFailed::class, function ($event) {
            return $event->workflowRun->id === $this->workflowRun->id;
        });

        $this->assertDatabaseHas(WorkflowRun::class, [
            'id' => $this->workflowRun->id,
            'workflow_run_status_id' => WorkflowRunStatus::FAILED
        ]);

    }

    public function test_that_we_can_schedule_the_next_run()
    {
        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowRunnerJob($this->workflowRun);
        $job->scheduleNextRun();

        $this->assertDatabaseHas(WorkflowRun::class, [
            'id' => $this->workflowRun->id,
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
            'next_run_at' => now()->startOfSecond()->addSeconds($this->workflow->retry_interval),
        ]);
    }

    public function test_that_if_next_run_is_already_scheduled_we_wont_schedule_it_again(): void
    {
        $this->travelTo($this->workflowRun->next_run_at->subHour());
        $job = new WorkflowRunnerJob($this->workflowRun);
        $job->scheduleNextRun();

        $this->assertDatabaseHas(WorkflowRun::class, [
            'id' => $this->workflowRun->id,
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
            'next_run_at' => $this->workflowRun->next_run_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_that_we_can_get_middleware_from_a_workflow_event(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function test_that_we_will_execute_a_workflow_step_from_the_first_passing_workflow_transition(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }

    public function test_that_we_will_execute_multiple_sequential_workflow_steps_in_a_single_run(): void
    {
        $this->markTestSkipped('Not implemented yet');
    }
}
