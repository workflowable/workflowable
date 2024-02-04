<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflowable\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Workflowable;

class ProcessReadyWorkflowProcessesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflowable:process-ready-workflow-processes';

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
            ->readyToRun()
            ->orderByPriority('desc')
            ->eachById(function (WorkflowProcess $workflowProcess) {
                $workflowEventAction = GetWorkflowEventImplementationAction::make()->handle($workflowProcess->workflow->workflow_event_id);
                try {
                    if (Workflowable::canDispatchWorkflowProcess($workflowProcess)) {
                        Workflowable::dispatchProcess($workflowProcess, $workflowEventAction->getQueue());
                    }
                } catch (WorkflowSwapException $swapException) {
                    // Indicate that we skipped a specific workflow process because it's impacted by a swap
                    $this->error($swapException->getMessage());
                }
            });

        return self::SUCCESS;
    }
}
