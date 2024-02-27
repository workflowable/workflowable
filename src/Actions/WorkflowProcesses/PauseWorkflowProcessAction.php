<?php

namespace Workflowable\Workflowable\Actions\WorkflowProcesses;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessPaused;
use Workflowable\Workflowable\Models\WorkflowProcess;

class PauseWorkflowProcessAction extends AbstractAction
{
    /**
     * Pauses a workflow process so that it won't be picked up by the workflow process runner
     *
     * @throws \Exception
     */
    public function handle(WorkflowProcess $workflowProcess): WorkflowProcess
    {
        if ($workflowProcess->workflow_process_status_id != WorkflowProcessStatusEnum::PENDING) {
            throw new \Exception('Workflow process is not pending');
        }

        $workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::PAUSED;
        $workflowProcess->save();

        WorkflowProcessPaused::dispatch($workflowProcess);

        return $workflowProcess;
    }
}
