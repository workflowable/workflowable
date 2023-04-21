<?php

namespace Workflowable\Workflow\Abstracts;

use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Traits\ValidatesWorkflowParameters;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesWorkflowParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
}
