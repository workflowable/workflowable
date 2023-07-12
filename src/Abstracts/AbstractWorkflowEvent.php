<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Traits\ValidatesWorkflowableParameters;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesWorkflowableParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getQueue(): string
    {
        return config('workflowable.queue');
    }
}
