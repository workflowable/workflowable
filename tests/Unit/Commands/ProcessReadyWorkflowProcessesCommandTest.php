<?php

namespace Workflowable\Workflowable\Tests\Unit\Commands;

use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Commands\ProcessReadyWorkflowProcessesCommand;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Jobs\WorkflowProcessRunnerJob;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcess;

class ProcessReadyWorkflowProcessesCommandTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_will_dispatch_processes_for_processes_that_are_ready_to_run()
    {
        Queue::fake();

        $command = new ProcessReadyWorkflowProcessesCommand();

        $workflowProcess = WorkflowProcess::factory()
            ->withWorkflow($this->workflow)
            ->withWorkflowProcessStatus(WorkflowProcessStatusEnum::PENDING)
            ->create([
                'next_run_at' => now()->subSecond(),
            ]);

        $command->handle();

        Queue::assertPushed(WorkflowProcessRunnerJob::class, function (WorkflowProcessRunnerJob $job) use ($workflowProcess) {
            return $job->workflowProcess->id === $workflowProcess->id;
        });
    }

    public function test_that_we_not_will_dispatch_processes_only_for_processes_that_are_not_ready_to_run()
    {
        Queue::fake();

        $command = new ProcessReadyWorkflowProcessesCommand();

        $command->handle();

        Queue::assertNotPushed(WorkflowProcessRunnerJob::class, function (WorkflowProcessRunnerJob $job) {
            return $job->workflowProcess->id === $this->workflowProcess->id;
        });
    }

    public function test_that_we_will_not_dispatch_if_a_workflow_swap_is_in_process()
    {
        $workflowSwap = WorkflowSwap::factory()
            ->withFromWorkflow($this->workflow)
            ->withToWorkflow($this->workflow)
            ->withWorkflowSwapStatus(WorkflowSwapStatusEnum::Processing)
            ->create();

        Queue::fake();

        $command = new ProcessReadyWorkflowProcessesCommand();

        $workflowProcess = WorkflowProcess::factory()
            ->withWorkflow($this->workflow)
            ->withWorkflowProcessStatus(WorkflowProcessStatusEnum::PENDING)
            ->create([
                'next_run_at' => now()->subSecond(),
            ]);

        $command->handle();

        Queue::assertNothingPushed();
    }
}
