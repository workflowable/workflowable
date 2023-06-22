<?php

namespace Workflowable\WorkflowEngine\Abstracts;

use Workflowable\WorkflowEngine\Contracts\WorkflowConditionTypeContract;
use Workflowable\WorkflowEngine\Traits\ValidatesWorkflowParameters;

abstract class AbstractWorkflowConditionType implements WorkflowConditionTypeContract
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
