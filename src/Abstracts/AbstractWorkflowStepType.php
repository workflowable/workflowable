<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflowable\Traits\ValidatesParameters;

abstract class AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    use ValidatesParameters;

    public function getRequiredWorkflowEventParameterKeys(): array
    {
        return [];
    }
}
