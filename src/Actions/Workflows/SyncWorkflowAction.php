<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Workflowable\Workflow\Actions\WorkflowSteps\CreateWorkflowStepAction;
use Workflowable\Workflow\Actions\WorkflowSteps\UpdateWorkflowStepAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowData;
use Workflowable\Workflow\Models\Workflow;

class SyncWorkflowAction
{
    public function handle(WorkflowData $workflowData): Workflow
    {
        // Look at workflow step and see if it has been created by looking at UX defined uuid
        // If it has been created, then update the workflow step
        // If it has not been created, then create the workflow step
        // If it has been deleted, then delete the workflow step
        $this->handleWorkflowSteps($workflowData);

        // Look at workflow transition and see if it has been created by looking at UX defined uuid
        // If it has been created, then update the workflow transition
        // If it has not been created, then create the workflow transition
        // If it has been deleted, then delete the workflow transition

        // Look at workflow condition and see if it has been created by looking at UX defined uuid
        // If it has been created, then update the workflow condition
        // If it has not been created, then create the workflow condition
        // If it has been deleted, then delete the workflow condition

        return $workflowData->workflow;
    }

    protected function handleWorkflowSteps(WorkflowData $workflowData): void
    {
        $seenWorkflowStepUxUuids = [];

        foreach ($workflowData->workflowSteps as $workflowStep) {
            $seenWorkflowStepUxUuids[] = $workflowStep->ux_uuid;

            $workflowStepModel = $workflowData->workflow->workflowSteps()->where('ux_uuid', $workflowStep->ux_uuid)->first();

            if ($workflowStepModel) {
                /** @var UpdateWorkflowStepAction $updateWorkflowStepAction */
                $updateWorkflowStepAction = app(UpdateWorkflowStepAction::class);
                $updateWorkflowStepAction->handle(
                    $workflowStepModel,
                    $workflowStep->parameters,
                    $workflowStep->name,
                    $workflowStep->description
                );
            } else {
                /** @var CreateWorkflowStepAction $createWorkflowStepAction */
                $createWorkflowStepAction = app(CreateWorkflowStepAction::class);
                $workflowStepModel = $createWorkflowStepAction->handle(
                    $workflowData->workflow,
                    $workflowStep->workflow_step_type_id,
                    $workflowStep->parameters,
                    $workflowStep->name,
                    $workflowStep->description
                );
            }

            $workflowStep->workflow_step_ui_uuid = $workflowStepModel->ui_uuid;
        }
    }

    protected function handleWorkflowTransitions(WorkflowData $workflowData): void
    {

    }

    protected function handleWorkflowConditions(WorkflowData $workflowData): void
    {

    }
}
