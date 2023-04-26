<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Traits\CreatesWorkflowConditions;

class CreateWorkflowTransitionAction
{
    use CreatesWorkflowConditions;

    /**
     * @param Workflow|int $workflow
     * @param WorkflowStep|int $fromWorkflowStep
     * @param WorkflowStep|int $toWorkflowStep
     * @param string $friendlyName
     * @param int $ordinal
     * @return WorkflowTransition
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowConditionException
     * @throws WorkflowStepException
     */
    public function handle(Workflow|int $workflow, WorkflowStep|int $fromWorkflowStep, WorkflowStep|int $toWorkflowStep, string $friendlyName, int $ordinal): WorkflowTransition
    {
        if ($fromWorkflowStep instanceof WorkflowStep && $fromWorkflowStep->workflow_id !== $workflow->id) {
            throw WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
        }

        if ($toWorkflowStep instanceof WorkflowStep && $toWorkflowStep->workflow_id !== $workflow->id) {
            throw WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
        }

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
