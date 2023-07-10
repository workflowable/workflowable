<?php

namespace Workflowable\WorkflowEngine\Abstracts;

use Workflowable\WorkflowEngine\Contracts\WorkflowEventContract;
use Workflowable\WorkflowEngine\Traits\ValidatesWorkflowEngineParameters;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesWorkflowEngineParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getQueue(): string
    {
        return 'default';
    }
}
