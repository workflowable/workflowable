<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflowable\Traits\ValidatesInputParameters;

abstract class AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    use ValidatesInputParameters;

    public function getRequiredWorkflowEventParameterKeys(): array
    {
        return [];
    }
}
