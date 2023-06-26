<?php

namespace Workflowable\WorkflowEngine\Abstracts;

use Workflowable\WorkflowEngine\Contracts\WorkflowConditionTypeContract;
use Workflowable\WorkflowEngine\Traits\ValidatesWorkflowEngineParameters;

abstract class AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    use ValidatesWorkflowEngineParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getRequiredWorkflowEventParameterKeys(): array
    {
        return [];
    }
}
