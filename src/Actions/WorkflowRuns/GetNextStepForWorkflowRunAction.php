<?php

namespace Workflowable\Workflowable\Actions\WorkflowRuns;

use Workflowable\Workflowable\Actions\WorkflowTransitions\EvaluateWorkflowTransitionAction;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowStep;
use Workflowable\Workflowable\Models\WorkflowTransition;

/**
 * Finds the next step for the workflow run by ordering the transitions by ordinal and evaluating them until one passes
 */
class GetNextStepForWorkflowRunAction
{
    /**
     * Finds the next step for a workflow run
     *
     * @throws \Exception
     */
    public function handle(WorkflowRun $workflowRun): ?WorkflowStep
    {
        // Grab all the workflow transitions that start from the last workflow step
        $workflowTransitions = WorkflowTransition::query()
            ->with([
                'workflowConditions.workflowConditionType',
                'toWorkflowStep.workflowStepType',
            ])
            ->where('workflow_id', $workflowRun->workflow_id)
            ->where('from_workflow_step_id', $workflowRun->last_workflow_step_id)
            ->orderBy('ordinal')
            ->get();

        // Iterate through the workflow transitions and see if any of them pass
        foreach ($workflowTransitions as $workflowTransition) {
            /** @var EvaluateWorkflowTransitionAction $evaluateWorkflowTransitionAction */
            $evaluateWorkflowTransitionAction = app(EvaluateWorkflowTransitionAction::class);
            $isPassing = $evaluateWorkflowTransitionAction->handle($workflowRun, $workflowTransition);
            if ($isPassing) {
                return $workflowTransition->toWorkflowStep;
            }
        }

        // If we get here, then we have no passing workflow transitions
        return null;
    }
}
