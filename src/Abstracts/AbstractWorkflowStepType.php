<?php

namespace Workflowable\WorkflowEngine\Abstracts;

use Workflowable\WorkflowEngine\Contracts\WorkflowStepTypeContract;
use Workflowable\WorkflowEngine\Traits\ValidatesWorkflowParameters;

abstract class AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    use ValidatesWorkflowParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getRequiredWorkflowEventKeys(): array
    {
        return [];
    }
}
