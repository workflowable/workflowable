<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflowable\Traits\ValidatesWorkflowableParameters;

abstract class AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    use ValidatesWorkflowableParameters;

    public function getRequiredWorkflowEventParameterKeys(): array
    {
        return [];
    }
}
