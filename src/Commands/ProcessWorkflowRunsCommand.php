<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflowable\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\Workflowable\Facades\Workflowable;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessStatus;

class ProcessWorkflowProcesssCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflowable:process-runs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Looks for workflow runs that have reached their next run time and dispatches a'
        .' WorkflowRunnerJob for each.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        WorkflowProcess::query()
            ->with('workflow')
            ->where('next_run_at', '<=', now())
            ->where('workflow_process_status_id', WorkflowProcessStatus::PENDING)
            ->join('workflows', 'workflows.id', '=', 'workflow_runs.workflow_id')
            ->join('workflow_priorities', 'workflow_priorities.id', '=', 'workflows.workflow_priority_id')
            ->orderBy('workflow_priorities.priority', 'desc')
            ->eachById(function (WorkflowProcess $WorkflowProcess) {
                /** @var GetWorkflowEventImplementationAction $getWorkflowEventAction */
                $getWorkflowEventAction = app(GetWorkflowEventImplementationAction::class);
                $workflowEventAction = $getWorkflowEventAction->handle($WorkflowProcess->workflow->workflow_event_id);
                Workflowable::dispatchRun($WorkflowProcess, $workflowEventAction->getQueue());
            });

        return self::SUCCESS;
    }
}
