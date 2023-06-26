<?php

namespace Workflowable\WorkflowEngine\Traits;

use Illuminate\Support\Facades\DB;
use Workflowable\WorkflowEngine\Events\Workflows\WorkflowActivated;
use Workflowable\WorkflowEngine\Events\Workflows\WorkflowArchived;
use Workflowable\WorkflowEngine\Events\Workflows\WorkflowDeactivated;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowPriority;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowRunStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;

trait InteractsWithWorkflows
{
    public function createWorkflow(string $name, WorkflowEvent|int $workflowEvent, WorkflowPriority|int $workflowPriority, int $retryInterval = 300): Workflow
    {
        /** @var Workflow $workflow */
        $workflow = Workflow::query()->create([
            'name' => $name,
            'workflow_event_id' => $workflowEvent instanceof WorkflowEvent
                ? $workflowEvent->id
                : $workflowEvent,
            'workflow_priority_id' => $workflowPriority instanceof WorkflowPriority
                ? $workflowPriority->id
                : $workflowPriority,
            'workflow_status_id' => WorkflowStatus::DRAFT,
            'retry_interval' => $retryInterval,
        ]);

        return $workflow;
    }

    public function activateWorkflow(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id === WorkflowStatus::ACTIVE) {
            throw WorkflowException::workflowAlreadyActive();
        }

        $workflow->workflow_status_id = WorkflowStatus::ACTIVE;
        $workflow->save();

        WorkflowActivated::dispatch($workflow);

        return $workflow;
    }

    public function archiveWorkflow(Workflow $workflow): Workflow
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

    public function deactivateWorkflow(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id === WorkflowStatus::INACTIVE) {
            throw WorkflowException::workflowAlreadyInactive();
        }

        $workflow->workflow_status_id = WorkflowStatus::INACTIVE;
        $workflow->save();

        WorkflowDeactivated::dispatch($workflow);

        return $workflow;
    }

    public function cloneWorkflow(Workflow $workflow, string $newWorkflowName): Workflow
    {

    }

    public function swapWorkflow(Workflow $workflowToDeactivate, Workflow $workflowToActivate): Workflow
    {
        DB::statement('
            UPDATE `workflows`
                SET `workflow_status_id` = CASE
                    WHEN `id` = ? THEN ?
                    WHEN `id` = ? THEN ?
                    ELSE `workflow_status_id`
                END
                WHERE `id` IN (?, ?)
            ', [
            $workflowToDeactivate->id,
            WorkflowStatus::INACTIVE,
            $workflowToActivate->id,
            WorkflowStatus::ACTIVE,
            $workflowToDeactivate->id,
            $workflowToActivate->id,
        ]);

        $workflowToActivate->refresh();

        return $workflowToActivate;
    }
}
