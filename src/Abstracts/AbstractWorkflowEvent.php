<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Concerns\GeneratesNameAndAliases;
use Workflowable\Workflowable\Concerns\ValidatesInputTokens;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use GeneratesNameAndAliases;
    use ValidatesInputTokens;

    public function getQueue(): string
    {
        return config('workflowable.queue');
    }
}
