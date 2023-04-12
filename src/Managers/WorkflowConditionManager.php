<?php

namespace Workflowable\Workflow\Managers;

use Illuminate\Support\Facades\Validator;
use Workflowable\Workflow\Contracts\WorkflowConditionContract;
use Workflowable\Workflow\Contracts\WorkflowConditionManagerContract;

class WorkflowConditionManager implements WorkflowConditionManagerContract
{
    protected array $workflowConditions = [];

    public function register(WorkflowConditionContract $workflowCondition): self
    {
        $this->workflowConditions[$workflowCondition->getAlias()] = $workflowCondition;

        return $this;
    }

    public function getImplementations(): array
    {
        return $this->workflowConditions;
    }

    public function getImplementation(string $alias): WorkflowConditionContract
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
