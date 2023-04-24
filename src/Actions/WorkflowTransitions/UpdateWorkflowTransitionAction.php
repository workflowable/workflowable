<?php

namespace Workflowable\Workflow\Actions\WorkflowTransitions;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Traits\CreatesWorkflowConditions;

class UpdateWorkflowTransitionAction
{
    use CreatesWorkflowConditions;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowConditionException
     */
    public function handle(WorkflowTransition $workflowTransition, string $friendlyName, int $ordinal): WorkflowTransition
    {
        $workflowTransition->update([
            'friendly_name' => $friendlyName,
            'ordinal' => $ordinal,
        ]);

        // Delete all the existing conditions and replace with new ones
        $workflowTransition->workflowConditions()->delete();

        $this->createWorkflowConditions($workflowTransition);

        return $workflowTransition;
    }
}
