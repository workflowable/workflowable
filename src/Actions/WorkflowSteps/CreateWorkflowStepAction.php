<?php

namespace Workflowable\Workflow\Actions\WorkflowSteps;

use Workflowable\Workflow\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowStepType;

class CreateWorkflowStepAction
{
    /**
     * @param Workflow|int $workflow
     * @param WorkflowStepType|int|string $workflowStepType
     * @param array $parameters
     * @param string|null $friendlyName
     * @param string|null $description
     *
     * @return WorkflowStep
     *
     * @throws WorkflowException
     * @throws WorkflowStepException
     */
    public function handle(
        Workflow|int $workflow,
        WorkflowStepType|int|string $workflowStepType,
        array $parameters = [],
        ?string $friendlyName = null,
        ?string $description = null
    ): WorkflowStep {
        $workflowStepTypeId = match (true) {
            is_int($workflowStepType) => $workflowStepType,
            is_string($workflowStepType) => WorkflowStepType::query()
                ->where('alias', $workflowStepType)
                ->firstOrFail()
                ->id,
            $workflowStepType instanceof WorkflowStepType => $workflowStepType->id,
        };

        if ($workflow->workflow_status_id !== WorkflowStatus::DRAFT) {
            throw WorkflowException::cannotModifyWorkflowNotInDraftState();
        }

        /** @var WorkflowStepTypeContract $workflowStepTypeContract */
        $workflowStepTypeContract = app(GetWorkflowStepTypeImplementationAction::class)->handle($workflowStepTypeId, $parameters);

        if (! $workflowStepTypeContract->hasValidParameters()) {
            throw WorkflowStepException::workflowStepTypeParametersInvalid();
        }

        /** @var WorkflowStep $workflowStep */
        $workflowStep = WorkflowStep::query()->create([
            'workflow_id' => $workflow instanceof Workflow
                ? $workflow->id
                : $workflow,
            'workflow_step_type_id' => $workflowStepTypeId,
            'friendly_name' => $friendlyName ?? 'N/A',
            'description' => $description,
            'parameters' => $parameters,
        ]);

        return $workflowStep;
    }
}
