<?php

namespace Workflowable\Workflow\Actions\WorkflowConditions;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Models\WorkflowCondition;

class CreateWorkflowConditionAction
{
    /**
     * @throws WorkflowConditionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(WorkflowConditionData $workflowConditionData): WorkflowCondition
    {
        /** @var GetWorkflowConditionTypeImplementationAction $getImplementationAction */
        $getImplementationAction = app(GetWorkflowConditionTypeImplementationAction::class);
        $workflowConditionTypeContract = $getImplementationAction->handle(
            $workflowConditionData->workflow_condition_type_id,
            $workflowConditionData->parameters
        );

        if (! $workflowConditionTypeContract->hasValidParameters()) {
            throw WorkflowConditionException::workflowConditionParametersInvalid();
        }

        return WorkflowCondition::query()->create([
            'workflow_condition_type_id' => $workflowConditionData->workflow_condition_type_id,
            'parameters' => $workflowConditionData->parameters,
            'workflow_transition_id' => $workflowConditionData->workflow_transition_id,
            'ordinal' => $workflowConditionData->ordinal,
        ]);
    }
}
