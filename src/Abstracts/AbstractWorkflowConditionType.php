<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Traits\ValidatesInputParameters;

abstract class AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    use ValidatesInputParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getRequiredWorkflowEventParameterKeys(): array
    {
        return [];
    }
}
