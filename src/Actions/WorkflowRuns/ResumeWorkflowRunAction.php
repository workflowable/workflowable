<?php

namespace Workflowable\Workflow\Actions\WorkflowRuns;

use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunResumed;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;

class ResumeWorkflowRunAction
{
    public function handle(WorkflowRun $workflowRun): WorkflowRun
    {
        if ($workflowRun->workflow_run_status_id != WorkflowRunStatus::PAUSED) {
            throw new \Exception('Workflow run is not paused');
        }

        $workflowRun->workflow_run_status_id = WorkflowRunStatus::PENDING;
        $workflowRun->save();

        WorkflowRunResumed::dispatch($workflowRun);

        return $workflowRun;
    }
}
