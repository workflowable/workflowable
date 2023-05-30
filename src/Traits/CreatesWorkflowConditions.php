<?php

namespace Workflowable\Workflow\Traits;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Workflowable\Workflow\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflow\Actions\WorkflowTransitions\CreateWorkflowTransitionAction;
use Workflowable\Workflow\Actions\WorkflowTransitions\UpdateWorkflowTransitionAction;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowTransition;

trait CreatesWorkflowConditions
{
    protected array $workflowConditions = [];

    /**
     * @param WorkflowConditionType|int|string $workflowConditionType
     * @param int $ordinal
     * @param array $parameters
     * @return UpdateWorkflowTransitionAction|CreateWorkflowTransitionAction
     */
    public function addWorkflowCondition(WorkflowConditionType|int|string $workflowConditionType, int $ordinal, array $parameters = []): UpdateWorkflowTransitionAction|CreateWorkflowTransitionAction
    {
        $this->workflowConditions[] = [
            'type' => $workflowConditionType,
            'parameters' => $parameters,
            'ordinal' => $ordinal,
        ];

        return $this;
    }

    public function getWorkflowConditions(): array
    {
        return $this->workflowConditions;
    }

    /**
     * Create the workflow conditions for the given workflow transition.
     *
     *
     *
     * @throws WorkflowConditionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createWorkflowConditions(WorkflowTransition $workflowTransition): array
    {
        // Keep a list of all workflow conditions that were created
        $createdWorkflowConditions = [];

        foreach ($this->workflowConditions as $condition) {
            /** @var GetWorkflowConditionTypeImplementationAction $action */
            $action = app(GetWorkflowConditionTypeImplementationAction::class);

            $workflowConditionTypeContract = $action->handle($condition['type'], $condition['parameters']);

            $isValid = $workflowConditionTypeContract->hasValidParameters();

            if (! $isValid) {
                throw WorkflowConditionException::workflowConditionParametersInvalid();
            }

            $createdWorkflowConditions[] = $workflowTransition->workflowConditions()->create([
                'workflow_condition_type_id' => match (true) {
                    $condition['type'] instanceof WorkflowConditionType => $condition['type']->id,
                    is_int($condition['type']) => $condition['type'],
                    is_string($condition['type']) => WorkflowConditionType::query()
                        ->where('alias', $condition['type'])
                        ->firstOrFail()
                        ?->id,
                },
                'ordinal' => $condition['ordinal'],
                'parameters' => $condition['parameters'],
            ]);
        }

        return $createdWorkflowConditions;
    }
}
