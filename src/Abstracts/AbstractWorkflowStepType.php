<?php

namespace Workflowable\Workflow\Abstracts;

use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Traits\ValidatesWorkflowParameters;

abstract class AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    use ValidatesWorkflowParameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
}
