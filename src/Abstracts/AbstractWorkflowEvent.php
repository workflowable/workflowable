<?php

namespace Workflowable\WorkflowEngine\Abstracts;

use Workflowable\WorkflowEngine\Contracts\WorkflowEventContract;
use Workflowable\WorkflowEngine\Traits\ValidatesParameters;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
}
