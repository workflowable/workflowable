<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Workflowable\Workflow\Contracts\EvaluateWorkflowTransitionActionContract;
use Workflowable\Workflow\Managers\WorkflowConditionManager;
use Workflowable\Workflow\Models\WorkflowTransition;

/**
 * Class EvaluateWorkflowTransitionAction
 */
class EvaluateWorkflowTransitionAction implements EvaluateWorkflowTransitionActionContract
{
    /**
     * Takes in a workflow transition and evaluates the conditions associated with it to determine if the workflow
     * action identified by the workflow transition can be executed.
     */
    public function handle(WorkflowTransition $workflowTransition): bool
    {
        if ($workflowTransition->workflowConditions->isEmpty()) {
            return true;
        }

        $isPassing = true;
        foreach ($workflowTransition->workflowConditions as $workflowCondition) {
            // Grab the class responsible for evaluating the workflow condition
            $workflowConditionAction = app(WorkflowConditionManager::class)->getWorkflowCondition($workflowCondition->workflowConditionType->alias);
            // Evaluate the workflow condition
            $isPassing = $workflowConditionAction->handle($workflowCondition);
            // If it fails, then we can stop evaluating the rest of the conditions
            if (! $isPassing) {
                break;
            }
        }

        /**
         * If we have a passing workflow transition, then we can now proceed to handling the next
         * workflow action identified by the workflow transition
         */
        return $isPassing;
    }
}
