<?php

namespace Workflowable\Workflow\Managers;

use Illuminate\Support\Facades\Validator;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Contracts\WorkflowEventManagerContract;
use Workflowable\Workflow\Exceptions\WorkflowEventException;

class WorkflowEventManager implements WorkflowEventManagerContract
{
    protected array $workflowEvents = [];

    public function register(WorkflowEventContract $workflowEventContract): WorkflowEventManagerContract
    {
        $this->workflowEvents[$workflowEventContract->getAlias()] = $workflowEventContract;

        return $this;
    }

    public function isRegistered(string $alias): bool
    {
        return isset($this->workflowEvents[$alias]);
    }

    public function getImplementations(): array
    {
        return $this->workflowEvents;
    }

    public function getImplementationByAlias(string $alias): WorkflowEventContract
    {
        if (! $this->isRegistered($alias)) {
            throw WorkflowEventException::workflowEventNotRegistered($alias);
        }

        return $this->workflowEvents[$alias];
    }

    public function getRules(string $alias): array
    {
        return $this->workflowEvents[$alias]->getRules();
    }

    public function isValid(string $alias, array $data): bool
    {
        return Validator::make($data, $this->workflowEvents[$alias]->getRules())->passes();
    }
}
