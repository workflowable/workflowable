<?php

namespace Workflowable\Workflowable\Tests\Commands;

use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Commands\ProcessScheduledWorkflowSwapsCommand;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Jobs\WorkflowSwapRunnerJob;
use Workflowable\Workflowable\Tests\HasWorkflowSwaps;
use Workflowable\Workflowable\Tests\TestCase;

class ProcessScheduledWorkflowSwapsCommandTest extends TestCase
{
    use HasWorkflowSwaps;

    public function test_we_will_dispatch_swaps_that_are_ready_to_run()
    {
        Queue::fake();

        $this->workflowSwap->workflow_swap_status_id = WorkflowSwapStatusEnum::Scheduled;
        $this->workflowSwap->scheduled_at = now()->subSecond();
        $this->workflowSwap->save();

        $command = new ProcessScheduledWorkflowSwapsCommand();

        $command->handle();

        Queue::assertPushed(WorkflowSwapRunnerJob::class, function (WorkflowSwapRunnerJob $job) {
            return $this->workflowSwap->id === $job->workflowSwap->id;
        });
    }

    public function test_we_will_not_dispatch_swaps_that_are_not_ready_to_run()
    {
        Queue::fake();

        $command = new ProcessScheduledWorkflowSwapsCommand();

        $command->handle();

        Queue::assertNothingPushed();
    }
}
