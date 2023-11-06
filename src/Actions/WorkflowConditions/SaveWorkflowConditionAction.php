<?php

namespace Workflowable\Workflowable\Actions\WorkflowConditions;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflowable\Exceptions\WorkflowConditionException;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowTransition;

class SaveWorkflowConditionAction extends AbstractAction
{
    protected WorkflowCondition $workflowCondition;

    public function __construct()
    {
        $this->workflowCondition = new WorkflowCondition();
    }

    public function withWorkflowCondition(WorkflowCondition $workflowCondition): self
    {
        $this->workflowCondition = $workflowCondition;

        return $this;
    }

    /**
     * @throws WorkflowConditionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(WorkflowTransition $workflowTransition, WorkflowConditionData $workflowConditionData): WorkflowCondition
    {
        $workflowConditionTypeContract = GetWorkflowConditionTypeImplementationAction::make()->handle(
            $workflowConditionData->workflow_condition_type_id,
            $workflowConditionData->parameters
        );

        if (! $workflowConditionTypeContract->hasValidParameters()) {
            throw WorkflowConditionException::workflowConditionParametersInvalid();
        }

        $this->workflowCondition->fill([
            'workflow_condition_type_id' => $workflowConditionData->workflow_condition_type_id,
            'workflow_transition_id' => $workflowTransition->id,
            'ordinal' => $workflowConditionData->ordinal,
        ]);

        $this->workflowCondition->save();

        if (! $this->workflowCondition->wasRecentlyCreated) {
            $this->workflowCondition->workflowConditionParameters()->delete();
        }

        foreach ($workflowConditionData->parameters as $name => $value) {
            $this->workflowCondition->workflowConditionParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $this->workflowCondition;
    }
}
