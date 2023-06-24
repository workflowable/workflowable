<?php

namespace Workflowable\WorkflowEngine\Abstracts;

use Workflowable\WorkflowEngine\Concerns\ValidatesParameters;
use Workflowable\WorkflowEngine\Contracts\WorkflowEventContract;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
}
