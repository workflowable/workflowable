<?php

namespace Workflowable\Workflowable\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCompleted;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessFailed;
use Workflowable\Workflowable\Jobs\WorkflowProcessRunnerJob;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityCompletion;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessStatus;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcessTests;

class WorkflowProcessRunnerJobTest extends TestCase
{
    use HasWorkflowProcessTests;

    public function test_that_we_can_mark_a_workflow_run_as_complete(): void
    {

        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowProcessRunnerJob($this->workflowProcess);
        Event::fake();
        $job->markRunComplete();

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'id' => $this->workflowProcess->id,
            'workflow_process_status_id' => WorkflowProcessStatus::COMPLETED,
            'completed_at' => now()->startOfSecond(),
        ]);

        Event::assertDispatched(WorkflowProcessCompleted::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id;
        });
    }

    public function test_that_we_can_mark_a_workflow_run_as_failed(): void
    {
        Event::fake();
        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowProcessRunnerJob($this->workflowProcess);

        $job->failed(new \Exception('Test exception'));

        Event::assertDispatched(WorkflowProcessFailed::class, function ($event) {
            return $event->workflowProcess->id === $this->workflowProcess->id;
        });

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'id' => $this->workflowProcess->id,
            'workflow_process_status_id' => WorkflowProcessStatus::FAILED,
        ]);

    }

    public function test_that_we_can_schedule_the_next_run()
    {
        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowProcessRunnerJob($this->workflowProcess);
        $job->scheduleNextRun();

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'id' => $this->workflowProcess->id,
            'workflow_process_status_id' => WorkflowProcessStatus::PENDING,
            'next_run_at' => now()->startOfSecond()->addSeconds($this->workflow->retry_interval),
        ]);
    }

    public function test_that_if_next_run_is_already_scheduled_we_wont_schedule_it_again(): void
    {
        $this->travelTo($this->workflowProcess->next_run_at->subHour());
        $job = new WorkflowProcessRunnerJob($this->workflowProcess);
        $job->scheduleNextRun();

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'id' => $this->workflowProcess->id,
            'workflow_process_status_id' => WorkflowProcessStatus::PENDING,
            'next_run_at' => $this->workflowProcess->next_run_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_that_we_can_get_generate_a_without_overlapping_lock_for_workflow_process_lock_key(): void
    {
        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);

        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeFake::class,
        ]);

        $job = new WorkflowProcessRunnerJob($this->workflowProcess);
        $lockKey = $job->getWorkflowProcessLockKey();
        $this->assertEquals($this->workflowEvent->alias, $lockKey);

        $middlewares = $job->middleware();
        $expectedMiddlewarePrefix = 'laravel-queue-overlap:';
        $expectedOverlapKeys = [
            $this->workflowProcess->id,
            $this->workflowEvent->alias,
        ];

        $this->assertCount(count($expectedOverlapKeys), $middlewares);

        foreach ($middlewares as $key => $middleware) {
            $this->assertEquals($expectedMiddlewarePrefix, $middleware->prefix);
            $this->assertContains($middleware->key, $expectedOverlapKeys);
        }
    }

    public function test_that_we_can_process_the_next_activity_in_a_workflow()
    {
        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeFake::class,
        ]);
        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowProcessRunnerJob($this->workflowProcess);
        $job->handle();

        $this->assertDatabaseHas(WorkflowActivityCompletion::class, [
            'workflow_activity_id' => $this->toWorkflowActivity->id,
            'workflow_process_id' => $this->workflowProcess->id,
        ]);

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'id' => $this->workflowProcess->id,
            'last_workflow_activity_id' => $this->toWorkflowActivity->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_that_we_will_execute_multiple_sequential_workflow_activities_in_a_single_run(): void
    {
        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeFake::class,
        ]);

        $finalWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflow)
            ->create();

        WorkflowTransition::factory()
            ->withWorkflow($this->workflow)
            ->withFromWorkflowActivity($this->toWorkflowActivity)
            ->withToWorkflowActivity($finalWorkflowActivity)
            ->create();

        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowProcessRunnerJob($this->workflowProcess);
        $job->handle();

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'id' => $this->workflowProcess->id,
            'last_workflow_activity_id' => $finalWorkflowActivity->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
