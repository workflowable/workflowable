<?php

namespace Workflowable\Workflowable\Actions\WorkflowConditions;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflowable\Exceptions\WorkflowConditionException;
use Workflowable\Workflowable\Models\WorkflowCondition;

class CreateWorkflowConditionAction extends AbstractAction
{
    /**
     * @throws WorkflowConditionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(WorkflowConditionData $workflowConditionData): WorkflowCondition
    {
        /** @var GetWorkflowConditionTypeImplementationAction $getImplementationAction */
        $workflowConditionTypeContract = GetWorkflowConditionTypeImplementationAction::make()->handle(
            $workflowConditionData->workflow_condition_type_id,
            $workflowConditionData->parameters
        );

        if (! $workflowConditionTypeContract->hasValidParameters()) {
            throw WorkflowConditionException::workflowConditionParametersInvalid();
        }

        $workflowCondition = WorkflowCondition::query()->create([
            'workflow_condition_type_id' => $workflowConditionData->workflow_condition_type_id,
            'workflow_transition_id' => $workflowConditionData->workflow_transition_id,
            'ordinal' => $workflowConditionData->ordinal,
        ]);

        foreach ($workflowConditionData->parameters as $name => $value) {
            $workflowCondition->workflowConditionParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $workflowCondition;
    }
}
