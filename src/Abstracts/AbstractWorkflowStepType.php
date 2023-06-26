<?php

namespace Workflowable\WorkflowEngine\Abstracts;

use Workflowable\WorkflowEngine\Contracts\WorkflowStepTypeContract;
use Workflowable\WorkflowEngine\Traits\ValidatesWorkflowEngineParameters;

abstract class AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    use ValidatesWorkflowEngineParameters;

    public function getRequiredWorkflowEventParameterKeys(): array
    {
        return [];
    }
}
