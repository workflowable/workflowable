<?php

namespace Workflowable\Workflow\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunDispatched;
use Workflowable\Workflow\Jobs\WorkflowRunnerJob;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;

class ScheduledWorkflowRunDispatcherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:dispatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        WorkflowRun::query()
            ->where('workflow_run_status_id', WorkflowRunStatus::PENDING)
            ->where('next_run_at', '<=', now())
            ->chunkById(100, function ($workflowRuns) {
                foreach ($workflowRuns as $workflowRun) {
                    $workflowRun->workflow_run_status_id = WorkflowRunStatus::DISPATCHED;
                    $workflowRun->save();

                    WorkflowRunnerJob::dispatch($workflowRun);
                    WorkflowRunDispatched::dispatch($workflowRun);
                }
            }, 'id');
    }
}
