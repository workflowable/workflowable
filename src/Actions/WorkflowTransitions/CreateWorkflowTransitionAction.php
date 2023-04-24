<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Traits\CreatesWorkflowConditions;

class CreateWorkflowTransitionAction
{
    use CreatesWorkflowConditions;

    /**
     * @throws WorkflowConditionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Workflow|int $workflow, WorkflowStep|int $fromWorkflowStep, WorkflowStep|int $toWorkflowStep, string $friendlyName, int $ordinal): WorkflowTransition
    {
        if ($fromWorkflowStep instanceof WorkflowStep && $fromWorkflowStep->workflow_id !== $workflow->id) {
            throw WorkflowConditionException::workflowStepDoesNotBelongToWorkflow();
        }

        if ($toWorkflowStep instanceof WorkflowStep && $toWorkflowStep->workflow_id !== $workflow->id) {
            throw WorkflowConditionException::workflowStepDoesNotBelongToWorkflow();
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
