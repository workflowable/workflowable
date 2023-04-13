<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Workflowable\Workflow\Events\Workflows\WorkflowArchived;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowStatus;

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
