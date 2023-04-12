<?php

namespace Workflowable\Workflow\Managers;

use Workflowable\Workflow\Contracts\WorkflowActionContract;
use Workflowable\Workflow\Contracts\WorkflowActionManagerContract;

class WorkflowActionManager implements WorkflowActionManagerContract
{
    protected array $workflowActions = [];

    public function register(WorkflowActionContract $workflowActionContract): WorkflowActionManagerContract
    {
        $this->workflowActions[$workflowActionContract->getAlias()] = $workflowActionContract;

        return $this;
    }

    public function getImplementations(): array
    {
        return $this->workflowActions;
    }

    public function getImplementation(string $workflowActionAlias): WorkflowActionContract
    {
        return $this->workflowActions[$workflowActionAlias];
    }

    public function getRules(string $workflowActionAlias): array
    {
        return $this->getImplementation($workflowActionAlias)->getRules();
    }

    public function isValid(string $workflowActionAlias, array $data): bool
    {
        return validator($data, $this->getRules($workflowActionAlias))->passes();
    }

    public function isRegistered(string $workflowActionAlias): bool
    {
        return array_key_exists($workflowActionAlias, $this->workflowActions);
    }
}
