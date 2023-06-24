<?php

namespace Workflowable\WorkflowEngine\Abstracts;

use Workflowable\WorkflowEngine\Concerns\ValidatesParameters;
use Workflowable\WorkflowEngine\Contracts\WorkflowStepTypeContract;

abstract class AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    use ValidatesParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getRequiredWorkflowEventParameterKeys(): array
    {
        return [];
    }
}
