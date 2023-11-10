<?php

namespace Workflowable\Workflowable\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Workflowable\Workflowable\Middleware\CannotSwapWithRunningWorkflowProcesses;
use Workflowable\Workflowable\Models\WorkflowSwap;

class WorkflowSwapRunnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WorkflowSwap $workflowSwap)
    {
        //
    }

    public function middleware(): array
    {
        return [
            new CannotSwapWithRunningWorkflowProcesses(),
        ];
    }

    public function handle(): void
    {
        // Look for workflow processes that are currently not completed
    }
}
