<?php

namespace Workflowable\Workflowable\Actions\Workflows;

use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\Workflows\WorkflowArchived;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowProcess;

class ArchiveWorkflowAction extends AbstractAction
{
    public function handle(Workflow $workflow): Workflow
    {
        DB::transaction(function () use ($workflow) {
            if ($workflow->workflow_status_id !== WorkflowStatusEnum::DEACTIVATED) {
                throw WorkflowException::workflowCannotBeArchivedFromActiveState();
            }

            $hasActiveWorkflowProcesses = WorkflowProcess::query()
                ->where('workflow_id', $workflow->id)
                ->active()
                ->exists();

            if ($hasActiveWorkflowProcesses) {
                throw WorkflowException::cannotArchiveWorkflowWithActiveProcesses();
            }

            $workflow->workflow_status_id = WorkflowStatusEnum::ARCHIVED;
            $workflow->save();

            WorkflowArchived::dispatch($workflow);
        });

        return $workflow;
    }
}
