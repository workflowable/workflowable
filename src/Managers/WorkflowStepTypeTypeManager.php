<?php

namespace Workflowable\Workflow\Managers;

use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Contracts\WorkflowStepTypeManagerContract;

class WorkflowStepTypeTypeManager implements WorkflowStepTypeManagerContract
{
    protected array $workflowStepTypes = [];

    public function register(WorkflowStepTypeContract $workflowStepTypeContract): WorkflowStepTypeManagerContract
    {
        $this->workflowStepTypes[$workflowStepTypeContract->getAlias()] = $workflowStepTypeContract;

        return $this;
    }

    public function getImplementations(): array
    {
        return $this->workflowStepTypes;
    }

    public function getImplementation(string $alias): WorkflowStepTypeContract
    {
        return $this->workflowStepTypes[$alias];
    }

    public function getRules(string $alias): array
    {
        return $this->getImplementation($alias)->getRules();
    }

    public function isValid(string $alias, array $data): bool
    {
        return validator($data, $this->getRules($alias))->passes();
    }

    public function isRegistered(string $alias): bool
    {
        return array_key_exists($alias, $this->workflowStepTypes);
    }
}
