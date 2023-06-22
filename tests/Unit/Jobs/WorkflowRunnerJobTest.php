<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Event;
use Workflowable\WorkflowEngine\Events\WorkflowRuns\WorkflowRunCompleted;
use Workflowable\WorkflowEngine\Events\WorkflowRuns\WorkflowRunFailed;
use Workflowable\WorkflowEngine\Jobs\WorkflowRunnerJob;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowRunStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\WorkflowEngine\Tests\TestCase;
use Workflowable\WorkflowEngine\Tests\Traits\HasWorkflowRunTests;

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
            'workflow_run_status_id' => WorkflowRunStatus::FAILED,
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
        $this->markTestIncomplete('Not implemented yet');
    }

    public function test_that_we_can_process_the_next_step_in_a_workflow()
    {
        config()->set('workflow-engine.workflow_step_types', [
            WorkflowStepTypeFake::class,
        ]);
        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowRunnerJob($this->workflowRun);
        $job->handle();

        $this->assertDatabaseHas(WorkflowRun::class, [
            'id' => $this->workflowRun->id,
            'last_workflow_step_id' => $this->toWorkflowStep->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_that_we_will_execute_multiple_sequential_workflow_steps_in_a_single_run(): void
    {
        config()->set('workflow-engine.workflow_step_types', [
            WorkflowStepTypeFake::class,
        ]);

        $finalWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($this->workflow)
            ->create();

        WorkflowTransition::factory()
            ->withWorkflow($this->workflow)
            ->withFromWorkflowStep($this->toWorkflowStep)
            ->withToWorkflowStep($finalWorkflowStep)
            ->create();

        $this->travelTo(now()->startOfSecond());
        $job = new WorkflowRunnerJob($this->workflowRun);
        $job->handle();

        $this->assertDatabaseHas(WorkflowRun::class, [
            'id' => $this->workflowRun->id,
            'last_workflow_step_id' => $finalWorkflowStep->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
