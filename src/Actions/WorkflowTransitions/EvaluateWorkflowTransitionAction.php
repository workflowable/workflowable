<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowTransitions;

use Workflowable\WorkflowEngine\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\WorkflowEngine\Contracts\EvaluateWorkflowTransitionActionContract;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;

/**
 * Class EvaluateWorkflowTransitionAction
 */
class EvaluateWorkflowTransitionAction implements EvaluateWorkflowTransitionActionContract
{
    /**
     * Takes in a workflow transition and evaluates the conditions associated with it to determine if the workflow
     * action identified by the workflow transition can be executed.
     */
    public function handle(WorkflowRun $workflowRun, WorkflowTransition $workflowTransition): bool
    {
        if ($workflowTransition->workflowConditions->isEmpty()) {
            return true;
        }

        $isPassing = true;
        foreach ($workflowTransition->workflowConditions as $workflowCondition) {

            /** @var GetWorkflowConditionTypeImplementationAction $action */
            $action = app(GetWorkflowConditionTypeImplementationAction::class);

            // Grab the class responsible for evaluating the workflow condition
            $workflowConditionTypeAction = $action->handle($workflowCondition->workflow_condition_type_id, $workflowCondition->parameters);

            // Evaluate the workflow condition
            $isPassing = $workflowConditionTypeAction->handle($workflowRun, $workflowCondition);
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
