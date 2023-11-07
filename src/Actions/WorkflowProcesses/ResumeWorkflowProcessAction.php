<?php

namespace Workflowable\Workflowable\Actions\WorkflowProcesses;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessResumed;
use Workflowable\Workflowable\Models\WorkflowProcess;

class ResumeWorkflowProcessAction extends AbstractAction
{
    /**
     * Resumes a workflow process so that it can be picked up by the workflow process runner
     *
     * @throws \Exception
     */
    public function handle(WorkflowProcess $workflowProcess): WorkflowProcess
    {
        if ($workflowProcess->workflow_process_status_id != WorkflowProcessStatusEnum::PAUSED) {
            throw new \Exception('Workflow run is not paused');
        }

        $workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::PENDING;
        $workflowProcess->save();

        WorkflowProcessResumed::dispatch($workflowProcess);

        return $workflowProcess;
    }
}
