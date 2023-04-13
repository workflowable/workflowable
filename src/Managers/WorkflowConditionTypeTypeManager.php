<?php

namespace Workflowable\Workflow\Managers;

use Illuminate\Support\Facades\Validator;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeManagerContract;

class WorkflowConditionTypeTypeManager implements WorkflowConditionTypeManagerContract
{
    protected array $workflowConditions = [];

    public function register(WorkflowConditionTypeContract $workflowConditionTypeContract): self
    {
        $this->workflowConditions[$workflowConditionTypeContract->getAlias()] = $workflowConditionTypeContract;

        return $this;
    }

    public function getImplementations(): array
    {
        return $this->workflowConditions;
    }

    public function getImplementation(string $alias): WorkflowConditionTypeContract
    {
        return $this->workflowConditions[$alias];
    }

    public function getRules(string $alias): array
    {
        return $this->getImplementation($alias)->getRules();
    }

    public function isValid(string $alias, array $data): bool
    {
        return Validator::make($data, $this->getRules($alias))->passes();
    }

    public function isRegistered(string $alias): bool
    {
        return array_key_exists($alias, $this->workflowConditions);
    }
}
