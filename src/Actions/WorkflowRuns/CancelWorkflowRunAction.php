<?php

namespace Workflowable\Workflow\Actions\WorkflowRuns;

use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCancelled;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;

class CancelWorkflowRunAction
{
    public function handle(WorkflowRun $workflowRun): WorkflowRun
    {
        if ($workflowRun->workflow_run_status_id != WorkflowRunStatus::PENDING) {
            throw new \Exception('Workflow run is not pending');
        }

        $workflowRun->workflow_run_status_id = WorkflowRunStatus::CANCELLED;
        $workflowRun->save();

        WorkflowRunCancelled::dispatch($workflowRun);

        return $workflowRun;
    }
}
