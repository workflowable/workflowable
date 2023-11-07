<?php

namespace Workflowable\Workflowable\Actions\WorkflowProcesses;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCancelled;
use Workflowable\Workflowable\Models\WorkflowProcess;

class CancelWorkflowProcessAction extends AbstractAction
{
    /**
     * Cancels a workflow process so that it won't be picked up by the workflow process runner
     *
     * @throws \Exception
     */
    public function handle(WorkflowProcess $workflowProcess): WorkflowProcess
    {
        if ($workflowProcess->workflow_process_status_id != WorkflowProcessStatusEnum::PENDING) {
            throw new \Exception('Workflow run is not pending');
        }

        $workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::CANCELLED;
        $workflowProcess->save();

        WorkflowProcessCancelled::dispatch($workflowProcess);

        return $workflowProcess;
    }
}
