<?php

namespace Workflowable\Workflowable\Actions\WorkflowTransitions;

use Illuminate\Support\Str;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowConditions\SaveWorkflowConditionAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowTransition;

class SaveWorkflowTransitionAction extends AbstractAction
{
    protected WorkflowTransition $workflowTransition;

    public function __construct()
    {
        $this->workflowTransition = new WorkflowTransition();
    }

    public function withWorkflowTransition(WorkflowTransition $workflowTransition): self
    {
        $this->workflowTransition = $workflowTransition;

        return $this;
    }

    public function handle(WorkflowTransitionData $workflowTransitionData): WorkflowTransition
    {
        if ($workflowTransitionData->fromWorkflowActivity->workflow->workflow_status_id !== WorkflowStatusEnum::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        if ($workflowTransitionData->fromWorkflowActivity->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow();
        }

        if ($workflowTransitionData->toWorkflowActivity->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowActivityException::workflowActivityDoesNotBelongToWorkflow();
        }

        $this->workflowTransition = $this->workflowTransition->fill([
            'workflow_id' => $workflowTransitionData->workflowId,
            'from_workflow_activity_id' => $workflowTransitionData->fromWorkflowActivity->id,
            'to_workflow_activity_id' => $workflowTransitionData->toWorkflowActivity->id,
            'name' => $workflowTransitionData->name,
            'ordinal' => $workflowTransitionData->ordinal,
            'ux_uuid' => $workflowTransitionData->uxUuid ?? Str::uuid()->toString(),
        ]);

        $this->workflowTransition->save();

        foreach ($workflowTransitionData->workflowConditions as $workflowConditionData) {
            SaveWorkflowConditionAction::make()
                ->handle($this->workflowTransition, $workflowConditionData);
        }

        return $this->workflowTransition;
    }
}
