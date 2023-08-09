<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\Workflows\WorkflowActivated;
use Workflowable\Workflowable\Events\Workflows\WorkflowArchived;
use Workflowable\Workflowable\Events\Workflows\WorkflowDeactivated;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivityParameter;
use Workflowable\Workflowable\Models\WorkflowConditionParameter;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowTransition;

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
            'workflow_status_id' => WorkflowStatusEnum::DRAFT,
            'retry_interval' => $retryInterval,
        ]);

        return $workflow;
    }

    /**
     * @throws WorkflowException
     */
    public function activateWorkflow(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id === WorkflowStatusEnum::ACTIVE) {
            throw WorkflowException::workflowAlreadyActive();
        }

        $workflow->workflow_status_id = WorkflowStatusEnum::ACTIVE;
        $workflow->save();

        WorkflowActivated::dispatch($workflow);

        return $workflow;
    }

    /**
     * @throws WorkflowException
     */
    public function archiveWorkflow(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id !== WorkflowStatusEnum::DEACTIVATED) {
            throw WorkflowException::workflowCannotBeArchivedFromActiveState();
        }

        $hasActiveWorkflowRuns = WorkflowProcess::query()
            ->where('workflow_id', $workflow->id)
            ->whereNotIn('workflow_process_status_id', [
                WorkflowProcessStatusEnum::CANCELLED,
                WorkflowProcessStatusEnum::COMPLETED,
            ])->exists();

        if ($hasActiveWorkflowRuns) {
            throw WorkflowException::cannotArchiveWorkflowWithActiveProcesses();
        }

        $workflow->workflow_status_id = WorkflowStatusEnum::ARCHIVED;
        $workflow->save();

        WorkflowArchived::dispatch($workflow);

        return $workflow;
    }

    /**
     * @throws WorkflowException
     */
    public function deactivateWorkflow(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id === WorkflowStatusEnum::DEACTIVATED) {
            throw WorkflowException::workflowAlreadyDeactivated();
        }

        $workflow->workflow_status_id = WorkflowStatusEnum::DEACTIVATED;
        $workflow->save();

        WorkflowDeactivated::dispatch($workflow);

        return $workflow;
    }

    public function cloneWorkflow(Workflow $workflow, string $newWorkflowName): Workflow
    {
        $newWorkflow = $this->createWorkflow(
            $newWorkflowName,
            $workflow->workflow_event_id,
            $workflow->workflow_priority_id,
            $workflow->retry_interval
        );

        /**
         * I need to create a mapping between the old and new workflow activities
         *
         * @var array<int, int> $workflowActivityIdMap
         */
        $workflowActivityIdMap = [];
        $workflow->workflowActivities()->eachById(function ($workflowActivity) use (&$workflowActivityIdMap, $newWorkflow) {
            $newWorkflowActivity = $workflowActivity->replicate();
            $newWorkflowActivity->workflow_id = $newWorkflow->id;
            $newWorkflowActivity->save();

            WorkflowActivityParameter::query()
                ->insertUsing([
                    'workflow_activity_id', 'key', 'value'],
                    /**
                     * Grab all the existing workflow engine parameters for the workflow activity and insert
                     * them into the new workflow activity
                     */
                    WorkflowActivityParameter::query()
                        ->selectRaw('? as workflow_activity_id', [$newWorkflowActivity->id])
                        ->selectRaw('key')
                        ->selectRaw('value')
                        ->where('workflow_activity_id', $workflowActivity->id)
                );

            // Map the old workflow activity id to the new workflow activity id
            $workflowActivityIdMap[$workflowActivity->id] = $newWorkflowActivity->id;
        });

        // I need to create a mapping between the old and new workflow transitions
        $workflow->workflowTransitions()->with(['workflowConditions'])->eachById(function ($workflowTransition) use ($workflowActivityIdMap, $newWorkflow) {
            $newWorkflowTransition = new WorkflowTransition();
            $newWorkflowTransition->name = $workflowTransition->name;
            $newWorkflowTransition->workflow_id = $newWorkflow->id;
            $newWorkflowTransition->from_workflow_activity_id = $workflowActivityIdMap[$workflowTransition->from_workflow_activity_id] ?? null;
            $newWorkflowTransition->to_workflow_activity_id = $workflowActivityIdMap[$workflowTransition->to_workflow_activity_id];
            $newWorkflowTransition->ordinal = $workflowTransition->ordinal;
            $newWorkflowTransition->ux_uuid = $workflowTransition->ux_uuid;
            $newWorkflowTransition->save();

            $workflowTransition->workflowConditions->each(function ($workflowCondition) use ($newWorkflowTransition) {
                $newWorkflowCondition = $workflowCondition->replicate();
                $newWorkflowCondition->workflow_transition_id = $newWorkflowTransition->id;
                $newWorkflowCondition->save();

                // Copy the old workflow condition parameters into the new workflow condition
                WorkflowConditionParameter::query()
                    ->insertUsing([
                        'workflow_condition_id', 'key', 'value'],
                        WorkflowConditionParameter::query()
                            ->selectRaw('? as workflow_condition_id', [$newWorkflowCondition->id])
                            ->selectRaw('key')
                            ->selectRaw('value')
                            ->where('workflow_condition_id', $workflowCondition->id)
                    );
            });
        });

        // I need to bulk insert workflow conditions according to the mapping above

        return $newWorkflow;
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
            WorkflowStatusEnum::DEACTIVATED->value,
            $workflowToActivate->id,
            WorkflowStatusEnum::ACTIVE->value,
            $workflowToDeactivate->id,
            $workflowToActivate->id,
        ]);

        $workflowToActivate->refresh();

        return $workflowToActivate;
    }
}
