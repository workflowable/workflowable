<?php

namespace Workflowable\WorkflowEngine\Actions\Workflows;

use Workflowable\WorkflowEngine\Events\Workflows\WorkflowArchived;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowRunStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;

class ArchiveWorkflowAction
{
    /**
     * @throws WorkflowException
     */
    public function handle(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id !== WorkflowStatus::INACTIVE) {
            throw WorkflowException::workflowCannotBeArchivedFromActiveState();
        }

        $hasActiveWorkflowRuns = WorkflowRun::query()
            ->where('workflow_id', $workflow->id)
            ->whereNotIn('workflow_run_status_id', [
                WorkflowRunStatus::CANCELLED,
                WorkflowRunStatus::COMPLETED,
            ])->exists();

        if ($hasActiveWorkflowRuns) {
            throw WorkflowException::cannotArchiveWorkflowWithActiveRuns();
        }

        $workflow->workflow_status_id = WorkflowStatus::ARCHIVED;
        $workflow->save();

        WorkflowArchived::dispatch($workflow);

        return $workflow;
    }
}
