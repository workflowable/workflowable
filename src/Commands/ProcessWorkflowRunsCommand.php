<?php

namespace Workflowable\WorkflowEngine\Commands;

use Illuminate\Console\Command;
use Workflowable\WorkflowEngine\Facades\WorkflowEngine;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowRunStatus;

class ProcessWorkflowRunsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow-engine:process-runs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Looks for workflow runs that have reached their next run time and dispatches a WorkflowRunnerJob for each.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        WorkflowRun::query()
            ->where('next_run_at', '<=', now())
            ->where('workflow_run_status_id', WorkflowRunStatus::PENDING)
            ->join('workflows', 'workflows.id', '=', 'workflow_runs.workflow_id')
            ->join('workflow_priorities', 'workflow_priorities.id', '=', 'workflows.workflow_priority_id')
            ->orderBy('workflow_priorities.priority', 'desc')
            ->eachById(function (WorkflowRun $workflowRun) {
                WorkflowEngine::dispatchRun($workflowRun);
            });

        return self::SUCCESS;
    }
}
