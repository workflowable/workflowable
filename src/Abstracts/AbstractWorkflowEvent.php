<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Traits\ValidatesInputParameters;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesInputParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getQueue(): string
    {
        return config('workflowable.queue');
    }
}
