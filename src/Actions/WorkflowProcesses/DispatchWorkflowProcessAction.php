<?php

namespace Workflowable\Workflowable\Actions\WorkflowProcesses;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowSwaps\HasWorkflowSwapInProgressAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessDispatched;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Jobs\WorkflowProcessRunnerJob;
use Workflowable\Workflowable\Models\WorkflowProcess;

class DispatchWorkflowProcessAction extends AbstractAction
{
    /**
     * Dispatches a workflow process so that it can be picked up by the workflow process runner
     *
     * @throws WorkflowSwapException
     */
    public function handle(WorkflowProcess $workflowProcess, string $queue = 'default'): WorkflowProcess
    {
        if (HasWorkflowSwapInProgressAction::make()->handle($workflowProcess)) {
            throw WorkflowSwapException::workflowSwapInProcess();
        }

        // Identify the workflow run as being dispatched
        $workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::DISPATCHED;
        $workflowProcess->save();

        // Dispatch the workflow run
        WorkflowProcessRunnerJob::dispatch($workflowProcess)->onQueue($queue);
        WorkflowProcessDispatched::dispatch($workflowProcess);

        return $workflowProcess;
    }
}
