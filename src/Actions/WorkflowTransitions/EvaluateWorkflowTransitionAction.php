<?php

namespace Workflowable\Workflowable\Actions\WorkflowTransitions;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflowable\Contracts\EvaluateWorkflowTransitionActionContract;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowTransition;

/**
 * Class EvaluateWorkflowTransitionAction
 */
class EvaluateWorkflowTransitionAction extends AbstractAction implements EvaluateWorkflowTransitionActionContract
{
    /**
     * Takes in a workflow transition and evaluates the conditions associated with it to determine if the workflow
     * action identified by the workflow transition can be executed.
     */
    public function handle(WorkflowProcess $workflowProcess, WorkflowTransition $workflowTransition): bool
    {
        if ($workflowTransition->workflowConditions->isEmpty()) {
            return true;
        }

        $isPassing = true;
        foreach ($workflowTransition->workflowConditions as $workflowCondition) {
            // Grab the class responsible for evaluating the workflow condition
            $workflowConditionTypeAction = GetWorkflowConditionTypeImplementationAction::make()->handle(
                $workflowCondition->workflow_condition_type_id,
                $workflowCondition->parameters ?? []
            );

            // Evaluate the workflow condition
            $isPassing = $workflowConditionTypeAction->handle($workflowProcess, $workflowCondition);
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
