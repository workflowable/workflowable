<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Events\Workflows\WorkflowActivated;
use Workflowable\Workflowable\Events\Workflows\WorkflowArchived;
use Workflowable\Workflowable\Events\Workflows\WorkflowDeactivated;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConfigurationParameter;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunStatus;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowStep;
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
            'workflow_status_id' => WorkflowStatus::DRAFT,
            'retry_interval' => $retryInterval,
        ]);

        return $workflow;
    }

    /**
     * @throws WorkflowException
     */
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

    /**
     * @throws WorkflowException
     */
    public function archiveWorkflow(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id !== WorkflowStatus::DEACTIVATED) {
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

    /**
     * @throws WorkflowException
     */
    public function deactivateWorkflow(Workflow $workflow): Workflow
    {
        if ($workflow->workflow_status_id === WorkflowStatus::DEACTIVATED) {
            throw WorkflowException::workflowAlreadyDeactivated();
        }

        $workflow->workflow_status_id = WorkflowStatus::DEACTIVATED;
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
         * I need to create a mapping between the old and new workflow steps
         *
         * @var array<int, int> $workflowStepIdMap
         */
        $workflowStepIdMap = [];
        $workflow->workflowSteps()->eachById(function ($workflowStep) use (&$workflowStepIdMap, $newWorkflow) {
            $newWorkflowStep = $workflowStep->replicate();
            $newWorkflowStep->workflow_id = $newWorkflow->id;
            $newWorkflowStep->save();

            WorkflowConfigurationParameter::query()
                ->insertUsing([
                    'parameterizable_id', 'parameterizable_type', 'key', 'value', 'type'],
                    /**
                     * Grab all the existing workflow engine parameters for the workflow step and insert
                     * them into the new workflow step
                     */
                    WorkflowConfigurationParameter::query()
                        ->selectRaw('? as parameterizable_id', [$newWorkflowStep->id])
                        ->selectRaw('? as parameterizable_type', [WorkflowStep::class])
                        ->selectRaw('key')
                        ->selectRaw('value')
                        ->selectRaw('type')
                        ->where('parameterizable_id', $workflowStep->id)
                        ->where('parameterizable_type', WorkflowStep::class)
                );

            // Map the old workflow step id to the new workflow step id
            $workflowStepIdMap[$workflowStep->id] = $newWorkflowStep->id;
        });

        // I need to create a mapping between the old and new workflow transitions
        $workflow->workflowTransitions()->with(['workflowConditions'])->eachById(function ($workflowTransition) use ($workflowStepIdMap, $newWorkflow) {
            $newWorkflowTransition = new WorkflowTransition();
            $newWorkflowTransition->name = $workflowTransition->name;
            $newWorkflowTransition->workflow_id = $newWorkflow->id;
            $newWorkflowTransition->from_workflow_step_id = $workflowStepIdMap[$workflowTransition->from_workflow_step_id] ?? null;
            $newWorkflowTransition->to_workflow_step_id = $workflowStepIdMap[$workflowTransition->to_workflow_step_id];
            $newWorkflowTransition->ordinal = $workflowTransition->ordinal;
            $newWorkflowTransition->ux_uuid = $workflowTransition->ux_uuid;
            $newWorkflowTransition->save();

            $workflowTransition->workflowConditions->each(function ($workflowCondition) use ($newWorkflowTransition) {
                $newWorkflowCondition = $workflowCondition->replicate();
                $newWorkflowCondition->workflow_transition_id = $newWorkflowTransition->id;
                $newWorkflowCondition->save();

                // Copy the old workflow condition parameters into the new workflow condition
                WorkflowConfigurationParameter::query()
                    ->insertUsing([
                        'parameterizable_id', 'parameterizable_type', 'key', 'value'],
                        WorkflowConfigurationParameter::query()
                            ->selectRaw('? as parameterizable_id', [$newWorkflowCondition->id])
                            ->selectRaw('? as parameterizable_type', [WorkflowCondition::class])
                            ->selectRaw('key')
                            ->selectRaw('value')
                            ->where('parameterizable_id', $workflowCondition->id)
                            ->where('parameterizable_type', WorkflowCondition::class)
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
            WorkflowStatus::DEACTIVATED,
            $workflowToActivate->id,
            WorkflowStatus::ACTIVE,
            $workflowToDeactivate->id,
            $workflowToActivate->id,
        ]);

        $workflowToActivate->refresh();

        return $workflowToActivate;
    }
}
