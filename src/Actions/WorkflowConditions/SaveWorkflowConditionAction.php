<?php

namespace Workflowable\Workflowable\Actions\WorkflowConditions;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflowable\Exceptions\InvalidWorkflowParametersException;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConditionType;
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
     * @throws InvalidWorkflowParametersException
     */
    public function handle(WorkflowTransition $workflowTransition, WorkflowConditionData $workflowConditionData): WorkflowCondition
    {
        $workflowConditionType = WorkflowConditionType::query()->findOrFail($workflowConditionData->workflow_condition_type_id);
        $workflowConditionTypeContract = new ($workflowConditionType->class_name);

        $form = $workflowConditionTypeContract->makeForm()->fill($workflowConditionData->parameters);
        if (! $form->isValid()) {
            throw new InvalidWorkflowParametersException();
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

        foreach ($form->getValues() as $name => $value) {
            $this->workflowCondition->workflowConditionParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $this->workflowCondition;
    }
}
