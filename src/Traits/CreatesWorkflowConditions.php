<?php

namespace Workflowable\Workflow\Traits;

use Workflowable\Workflow\Actions\WorkflowTransitions\CreateWorkflowTransitionAction;
use Workflowable\Workflow\Actions\WorkflowTransitions\UpdateWorkflowTransitionAction;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeManagerContract;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowTransition;

trait CreatesWorkflowConditions
{
    protected array $workflowConditions = [];

    /**
     * @return UpdateWorkflowTransitionAction|CreateWorkflowTransitionAction|CreatesWorkflowConditions
     */
    public function addWorkflowCondition(WorkflowConditionType|int|string $workflowConditionType, int $ordinal, array $parameters = []): self
    {
        $this->workflowConditions[] = [
            'type' => $workflowConditionType,
            'parameters' => $parameters,
            'ordinal' => $ordinal,
        ];

        return $this;
    }

    /**
     * @throws WorkflowConditionException
     */
    public function createWorkflowConditions(WorkflowTransition $workflowTransition): array
    {
        $createdWorkflowConditions = [];

        foreach ($this->workflowConditions as $condition) {
            $type = $this->getWorkflowConditionType($condition['type']);
            $this->validateWorkflowCondition($type, $workflowTransition, $condition['parameters']);

            $workflowTransition->workflowConditions()->create([
                'workflow_condition_type_id' => $type->id,
                'ordinal' => $condition['ordinal'],
                'parameters' => $condition['parameters'],
            ]);
        }

        return $createdWorkflowConditions;
    }

    private function getWorkflowConditionType(WorkflowConditionType|int|string $type): WorkflowConditionType
    {
        if (is_int($type)) {
            $workflowConditionType = WorkflowConditionType::query()->findOrFail($type);
        } elseif (is_string($type)) {
            $workflowConditionType = WorkflowConditionType::query()->where('alias', $type)->firstOrFail();
        } else {
            $workflowConditionType = $type;
        }

        return $workflowConditionType;
    }

    /**
     * @throws WorkflowConditionException
     */
    private function validateWorkflowCondition(WorkflowConditionType $workflowConditionType, WorkflowTransition $workflowTransition, array $parameters): void
    {
        $manager = app(WorkflowConditionTypeManagerContract::class);

        if (! $manager->isRegistered($workflowConditionType->alias)) {
            throw WorkflowConditionException::workflowConditionTypeNotRegistered($workflowConditionType->alias);
        }

        $eventAlias = $manager->getWorkflowEventAlias($workflowConditionType->alias);
        if (! is_null($eventAlias) && $eventAlias !== $workflowTransition->workflow->workflowEvent->alias) {
            throw WorkflowConditionException::workflowConditionTypeNotEligibleForEvent($workflowConditionType->alias);
        }

        if (! $manager->isValidParameters($workflowConditionType->alias, $parameters)) {
            throw WorkflowConditionException::workflowConditionTypeParametersInvalid($workflowConditionType->alias);
        }
    }
}
