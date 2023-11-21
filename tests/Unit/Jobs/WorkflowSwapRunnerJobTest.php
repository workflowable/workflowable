<?php

namespace Workflowable\Workflowable\Tests\Unit\Jobs;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Workflowable\Workflowable\Actions\Workflows\ArchiveWorkflowAction;
use Workflowable\Workflowable\Actions\WorkflowSwaps\SwapWorkflowProcessAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapCompleted;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapProcessing;
use Workflowable\Workflowable\Jobs\WorkflowSwapRunnerJob;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcess;

class WorkflowSwapRunnerJobTest extends TestCase
{
    use HasWorkflowProcess;

    public static function workflow_process_statuses_that_prevent_swap_from_running_data_provider()
    {
        $testCases = [];
        foreach (WorkflowProcessStatusEnum::running() as $key => $case) {
            $testCases[WorkflowProcessStatusEnum::match($case)] = [$case];
        }

        return $testCases;
    }

    /**
     * @return void
     *
     * @dataProvider workflow_process_statuses_that_prevent_swap_from_running_data_provider
     */
    public function test_that_the_job_will_be_released_if_there_are_dispatched_or_running_workflow_processes(WorkflowProcessStatusEnum $status)
    {
        $workflowToSwapTo = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowSwap = WorkflowSwap::factory()
            ->withWorkflowSwapStatus(WorkflowSwapStatusEnum::Dispatched)
            ->withFromWorkflow($this->workflow)
            ->withToWorkflow($workflowToSwapTo)
            ->create();

        $this->workflowProcess->update([
            'workflow_process_status_id' => $status,
        ]);

        $job = (new WorkflowSwapRunnerJob($workflowSwap));
        // mocking underling job object.
        $job->job = $this->mock(Job::class, function (MockInterface $mock) {
            $mock->shouldReceive('release')->once()->with(30);
        });
        $job->handle();
    }

    public function test_that_we_will_dispatch_the_workflow_swap_processing_event_when_we_start_processing()
    {
        Event::fake();

        $workflowToSwapTo = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowSwap = WorkflowSwap::factory()
            ->withWorkflowSwapStatus(WorkflowSwapStatusEnum::Dispatched)
            ->withFromWorkflow($this->workflow)
            ->withToWorkflow($workflowToSwapTo)
            ->create();

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::COMPLETED,
        ]);

        $job = (new WorkflowSwapRunnerJob($workflowSwap));
        $job->handle();

        Event::assertDispatched(function (WorkflowSwapProcessing $event) use ($workflowSwap) {
            return $workflowSwap->id === $event->workflowSwap->id;
        });
    }

    public function test_that_we_will_dispatch_the_workflow_swap_completed_event_when_we_complete_processing()
    {
        Event::fake();

        $workflowToSwapTo = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowSwap = WorkflowSwap::factory()
            ->withWorkflowSwapStatus(WorkflowSwapStatusEnum::Dispatched)
            ->withFromWorkflow($this->workflow)
            ->withToWorkflow($workflowToSwapTo)
            ->create();

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::COMPLETED,
        ]);

        $job = (new WorkflowSwapRunnerJob($workflowSwap));
        $job->handle();

        Event::assertDispatched(function (WorkflowSwapCompleted $event) use ($workflowSwap) {
            return $workflowSwap->id === $event->workflowSwap->id;
        });
    }

    public function test_that_if_no_pending_processes_remain_we_will_archive_the_original_workflow()
    {
        $workflowToSwapTo = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowSwap = WorkflowSwap::factory()
            ->withWorkflowSwapStatus(WorkflowSwapStatusEnum::Dispatched)
            ->withFromWorkflow($this->workflow)
            ->withToWorkflow($workflowToSwapTo)
            ->create();

        $this->workflowProcess->update([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::COMPLETED,
        ]);

        $job = (new WorkflowSwapRunnerJob($workflowSwap));
        $job->handle();

        $this->assertDatabaseHas(Workflow::class, [
            'id' => $workflowSwap->from_workflow_id,
            'workflow_status_id' => WorkflowStatusEnum::ARCHIVED,
        ]);
    }

    public static function workflow_process_statuses_we_will_swap_data_provider()
    {
        $testCases = [];
        $diff = collect(WorkflowProcessStatusEnum::active())->map->value
            ->diff(collect(WorkflowProcessStatusEnum::running())->map->value);

        foreach ($diff as $key => $case) {
            $testCases[WorkflowProcessStatusEnum::match($case)] = [$case];
        }

        return $testCases;
    }

    /**
     * @return void
     *
     * @dataProvider workflow_process_statuses_we_will_swap_data_provider
     */
    public function test_that_we_will_convert_workflow_processes_with_status(int $status)
    {
        $workflowToSwapTo = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowSwap = WorkflowSwap::factory()
            ->withWorkflowSwapStatus(WorkflowSwapStatusEnum::Dispatched)
            ->withFromWorkflow($this->workflow)
            ->withToWorkflow($workflowToSwapTo)
            ->create();

        $this->workflowProcess->update([
            'workflow_process_status_id' => $status,
        ]);

        SwapWorkflowProcessAction::fake(function (MockInterface $mock) use ($workflowSwap) {
            $mock->shouldReceive('handle')
                ->withArgs(function ($workflowSwapReceived, $workflowProcessReceived) use ($workflowSwap) {
                    return $workflowSwapReceived->id == $workflowSwap->id
                        && $workflowProcessReceived->id == $this->workflowProcess->id;
                })
                ->once();
        });

        // Will not receive because we did not actually perform the transfer
        ArchiveWorkflowAction::fake(function (MockInterface $mock) {
            $mock->shouldReceive('handle')->withArgs(function (Workflow $workflow) {
                return $this->workflow->id === $workflow->id;
            })->never();
        });

        $job = (new WorkflowSwapRunnerJob($workflowSwap));
        $job->handle();
    }

    public static function workflow_process_statuses_we_will_not_swap_data_provider()
    {
        $testCases = [];
        foreach (WorkflowProcessStatusEnum::inactive() as $key => $case) {
            $testCases[WorkflowProcessStatusEnum::match($case)] = [$case];
        }

        return $testCases;
    }

    /**
     * @return void
     *
     * @dataProvider workflow_process_statuses_we_will_not_swap_data_provider
     */
    public function test_that_we_will_not_convert_workflow_processes_with_state(WorkflowProcessStatusEnum $status)
    {
        $workflowToSwapTo = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $workflowSwap = WorkflowSwap::factory()
            ->withWorkflowSwapStatus(WorkflowSwapStatusEnum::Dispatched)
            ->withFromWorkflow($this->workflow)
            ->withToWorkflow($workflowToSwapTo)
            ->create();

        $this->workflowProcess->update([
            'workflow_process_status_id' => $status,
        ]);

        SwapWorkflowProcessAction::fake(function (MockInterface $mock) use ($workflowSwap) {
            $mock->shouldReceive('handle')
                ->withArgs(function ($workflowSwapReceived, $workflowProcessReceived) use ($workflowSwap) {
                    return $workflowSwapReceived->id == $workflowSwap->id
                        && $workflowProcessReceived->id == $this->workflowProcess->id;
                })
                ->never();
        });

        ArchiveWorkflowAction::fake(function (MockInterface $mock) {
            $mock->shouldReceive('handle')->withArgs(function (Workflow $workflow) {
                return $this->workflow->id === $workflow->id;
            })->once();
        });

        $job = (new WorkflowSwapRunnerJob($workflowSwap));
        $job->handle();
    }
}
