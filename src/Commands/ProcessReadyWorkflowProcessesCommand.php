<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflowable\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Workflowable;

class ProcessReadyWorkflowProcessesCommand extends Command
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
    protected $description = 'Looks for workflow processes that have reached their next run time and dispatches a'
        .' WorkflowProcessRunnerJob for each.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        WorkflowProcess::query()
            ->with('workflow')
            ->where('next_run_at', '<=', now())
            ->where('workflow_process_status_id', WorkflowProcessStatusEnum::PENDING)
            ->join('workflows', 'workflows.id', '=', 'workflow_runs.workflow_id')
            ->join('workflow_priorities', 'workflow_priorities.id', '=', 'workflows.workflow_priority_id')
            ->orderBy('workflow_priorities.priority', 'desc')
            ->eachById(function (WorkflowProcess $WorkflowProcess) {
                $workflowEventAction = GetWorkflowEventImplementationAction::make()->handle($WorkflowProcess->workflow->workflow_event_id);
                Workflowable::dispatchProcess($WorkflowProcess, $workflowEventAction->getQueue());
            });

        return self::SUCCESS;
    }
}
