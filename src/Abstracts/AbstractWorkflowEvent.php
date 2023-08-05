<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Traits\ValidatesInputTokens;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesInputTokens;

    public function getQueue(): string
    {
        return config('workflowable.queue');
    }
}
