<?php

namespace Workflowable\Workflowable\Actions\WorkflowRuns;

use Workflowable\Workflowable\Actions\WorkflowTransitions\EvaluateWorkflowTransitionAction;
use Workflowable\Workflowable\Contracts\GetNextActivityForWorkflowRunActionContract;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowTransition;

/**
 * Finds the next activity for the workflow run by ordering the transitions by ordinal and evaluating them until one passes
 */
class GetNextActivityForWorkflowRunAction implements GetNextActivityForWorkflowRunActionContract
{
    /**
     * Finds the next activity for a workflow run
     *
     * @throws \Exception
     */
    public function handle(WorkflowRun $workflowRun): ?WorkflowActivity
    {
        // Grab all the workflow transitions that start from the last workflow activity
        $workflowTransitions = WorkflowTransition::query()
            ->with([
                'workflowConditions.workflowConditionType',
                'toWorkflowActivity.workflowActivityType',
            ])
            ->where('workflow_id', $workflowRun->workflow_id)
            ->where('from_workflow_activity_id', $workflowRun->last_workflow_activity_id)
            ->orderBy('ordinal')
            ->get();

        /**
         * Reload the workflow run to ensure we have the latest data.  This is necessary so that if any output
         * parameters were set by the previous workflow activity, we have them available to evaluate the workflow
         */
        $workflowRun->refresh();

        // Iterate through the workflow transitions and see if any of them pass
        foreach ($workflowTransitions as $workflowTransition) {
            /** @var EvaluateWorkflowTransitionAction $evaluateWorkflowTransitionAction */
            $evaluateWorkflowTransitionAction = app(EvaluateWorkflowTransitionAction::class);
            $isPassing = $evaluateWorkflowTransitionAction->handle($workflowRun, $workflowTransition);
            if ($isPassing) {
                return $workflowTransition->toWorkflowActivity;
            }
        }

        // If we get here, then we have no passing workflow transitions
        return null;
    }
}
