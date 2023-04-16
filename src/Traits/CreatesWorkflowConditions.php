<?php

namespace Workflowable\Workflow\Traits;

use Workflowable\Workflow\Contracts\WorkflowConditionTypeManagerContract;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Models\WorkflowTransition;

trait CreatesWorkflowConditions
{
    protected array $workflowConditions = [];

    public function addWorkflowCondition(WorkflowConditionType|int|string $workflowConditionType, int $ordinal, array $parameters = []): self
    {
        $this->workflowConditions[] = [
            'type' => $workflowConditionType,
            'parameters' => $parameters,
            'ordinal' => $ordinal
        ];

        return $this;
    }

    public function createWorkflowConditions(WorkflowTransition $workflowTransition): void
    {
        foreach ($this->workflowConditions as $condition) {
            /** @var WorkflowConditionTypeManagerContract $manager */
            $manager = app(WorkflowConditionTypeManagerContract::class);

            $type = $this->getWorkflowConditionType($condition['type']);
            $this->validateWorkflowCondition($manager, $type, $workflowTransition, $condition['parameters']);

            $workflowTransition->workflowConditions()->create([
                'workflow_condition_type_id' => $type->id,
                'ordinal' => $condition['ordinal'],
                'parameters' => $condition['parameters'],
            ]);
        }
    }

    private function getWorkflowConditionType($type): WorkflowConditionType
    {
        if (is_int($type)) {
            return WorkflowConditionType::findOrFail($type);
        } elseif (is_string($type)) {
            return WorkflowConditionType::where('alias', $type)->firstOrFail();
        } else {
            return $type;
        }
    }

    private function validateWorkflowCondition($manager, $type, $workflowTransition, $parameters): void
    {
        if (!$manager->isRegistered($type->alias)) {
            throw WorkflowConditionException::workflowConditionTypeNotRegistered($type->alias);
        }

        $eventAlias = $manager->getWorkflowEventAlias($type->alias);
        if (!is_null($eventAlias) && $eventAlias !== $workflowTransition->workflow->workflowEvent->alias) {
            throw WorkflowConditionException::workflowConditionTypeNotEligibleForEvent($type->alias);
        }

        if (!$manager->isValidParameters($type->alias, $parameters)) {
            throw WorkflowConditionException::workflowConditionTypeParametersInvalid($type->alias);
        }
    }
}
