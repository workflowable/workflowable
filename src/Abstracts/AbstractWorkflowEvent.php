<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Traits\ValidatesParameters;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getQueue(): string
    {
        return config('workflowable.queue');
    }
}
