<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Traits\CreatesWorkflowConditions;

class UpdateWorkflowTransitionAction
{
    use CreatesWorkflowConditions;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowConditionException
     * @throws WorkflowException
     */
    public function handle(WorkflowTransition $workflowTransition, string $name, int $ordinal): WorkflowTransition
    {
        if ($workflowTransition->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        $workflowTransition->update([
            'name' => $name,
            'ordinal' => $ordinal,
        ]);

        // Delete all the existing conditions and replace with new ones
        $workflowTransition->workflowConditions()->delete();

        $this->createWorkflowConditions($workflowTransition);

        return $workflowTransition;
    }
}
