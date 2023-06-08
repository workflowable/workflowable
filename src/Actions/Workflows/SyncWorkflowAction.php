<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Actions\WorkflowSteps\CreateWorkflowStepAction;
use Workflowable\Workflow\Actions\WorkflowSteps\UpdateWorkflowStepAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowData;
use Workflowable\Workflow\DataTransferObjects\WorkflowStepData;
use Workflowable\Workflow\DataTransferObjects\WorkflowTransitionData;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Traits\SyncsWorkflowConditions;

class SyncWorkflowAction
{
    use SyncsWorkflowConditions;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowException
     * @throws WorkflowStepException
     */
    public function handle(WorkflowData $workflowData): Workflow
    {
        $this->handleWorkflowSteps($workflowData);

        $this->handleWorkflowTransitions($workflowData);

        return $workflowData->workflow->fresh();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WorkflowException
     * @throws WorkflowStepException
     */
    protected function handleWorkflowSteps(WorkflowData $workflowData): void
    {
        foreach ($workflowData->workflowSteps as $workflowStep) {

            /** @var WorkflowStep|null $workflowStepModel */
            $workflowStepModel = $workflowData->workflow->workflowSteps()
                ->where('ux_uuid', $workflowStep->ux_uuid)
                ->first();

            if ($workflowStepModel) {
                /** @var UpdateWorkflowStepAction $updateWorkflowStepAction */
                $updateWorkflowStepAction = app(UpdateWorkflowStepAction::class);
                $updateWorkflowStepAction->handle($workflowStepModel, new WorkflowStepData());
            } else {
                /** @var CreateWorkflowStepAction $createWorkflowStepAction */
                $createWorkflowStepAction = app(CreateWorkflowStepAction::class);
                $createWorkflowStepAction->handle($workflowData->workflow, new WorkflowStepData());
            }
        }
    }

    protected function handleWorkflowTransitions(WorkflowData $workflowData): void
    {
        // TODO: Do some kind of validation to make sure that all the conditions are eligible for the workflow.

        WorkflowTransition::query()
            ->where('workflow_id', $workflowData->workflow->id)
            ->delete();

        $workflowTransitionUpsertData = collect($workflowData->workflowTransitions)->map(function (WorkflowTransitionData $workflowTransitionData) {
            return [
                'workflow_id' => $workflowTransitionData->workflowId,
                'name' => $workflowTransitionData->name,
                'ordinal' => $workflowTransitionData->ordinal,
                'ux_uuid' => $workflowTransitionData->uxUuid,
                'from_workflow_step' => $workflowTransitionData->fromWorkflowStep->id,
                'to_workflow_step' => $workflowTransitionData->toWorkflowStep->id,
            ];
        });

        WorkflowTransition::query()
            ->insert($workflowTransitionUpsertData->toArray());

        $workflowConditionDataCollection = collect($workflowData->workflowTransitions)->pluck('workflow_conditions')->flatten();
        $this->syncWorkflowConditions($workflowConditionDataCollection);
    }
}
