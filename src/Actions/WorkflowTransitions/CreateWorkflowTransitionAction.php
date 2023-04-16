<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Traits\CreatesWorkflowConditions;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;

class CreateWorkflowTransitionAction
{
    use CreatesWorkflowConditions;

    /**
     * @param Workflow|int $workflow
     * @param WorkflowStep|int $fromWorkflowStep
     * @param WorkflowStep|int $toWorkflowStep
     * @param string $friendlyName
     * @param int $ordinal
     *
     * @return WorkflowTransition
     *
     * @throws WorkflowConditionException
     */
    public function handle(Workflow|int $workflow, WorkflowStep|int $fromWorkflowStep, WorkflowStep|int $toWorkflowStep, string $friendlyName, int $ordinal): WorkflowTransition
    {
        /** @var WorkflowTransition $workflowTransition */
        $workflowTransition = WorkflowTransition::query()->create([
            'workflow_id' => $workflow instanceof Workflow
                ? $workflow->id
                : $workflow,
            'from_workflow_step_id' => $fromWorkflowStep instanceof WorkflowStep
                ? $fromWorkflowStep->id
                : $fromWorkflowStep,
            'to_workflow_step_id' => $toWorkflowStep instanceof WorkflowStep
                ? $toWorkflowStep->id
                : $toWorkflowStep,
            'friendly_name' => $friendlyName,
            'ordinal' => $ordinal,
        ]);

        $this->createWorkflowConditions($workflowTransition);

        return $workflowTransition;
    }
}
