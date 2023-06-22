<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowTransitions;

use Illuminate\Support\Str;
use Workflowable\WorkflowEngine\DataTransferObjects\WorkflowTransitionData;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Exceptions\WorkflowStepException;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;

class CreateWorkflowTransitionAction
{
    /**
     * @throws WorkflowException
     * @throws WorkflowStepException
     */
    public function handle(WorkflowTransitionData $workflowTransitionData): WorkflowTransition
    {
        if ($workflowTransitionData->fromWorkflowStep->workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        if ($workflowTransitionData->fromWorkflowStep->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
        }

        if ($workflowTransitionData->toWorkflowStep->workflow_id !== $workflowTransitionData->workflowId) {
            throw WorkflowStepException::workflowStepDoesNotBelongToWorkflow();
        }

        /** @var WorkflowTransition $workflowTransition */
        $workflowTransition = WorkflowTransition::query()->create([
            'workflow_id' => $workflowTransitionData->workflowId,
            'from_workflow_step_id' => $workflowTransitionData->fromWorkflowStep->id,
            'to_workflow_step_id' => $workflowTransitionData->toWorkflowStep->id,
            'name' => $workflowTransitionData->name,
            'ordinal' => $workflowTransitionData->ordinal,
            'ux_uuid' => $workflowTransitionData->uxUuid ?? Str::uuid()->toString(),
        ]);

        return $workflowTransition;
    }
}
