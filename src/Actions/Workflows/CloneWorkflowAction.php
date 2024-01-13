<?php

namespace Workflowable\Workflowable\Actions\Workflows;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivityParameter;
use Workflowable\Workflowable\Models\WorkflowConditionParameter;
use Workflowable\Workflowable\Models\WorkflowTransition;

class CloneWorkflowAction extends AbstractAction
{
    public function handle(Workflow $workflow, string $newWorkflowName): Workflow
    {
        $newWorkflow = SaveWorkflowAction::make()
            ->handle(
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

        return $newWorkflow;
    }
}
