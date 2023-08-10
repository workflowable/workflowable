<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Concerns\GeneratesNameAndAliases;
use Workflowable\Workflowable\Concerns\ValidatesInputTokens;

abstract class AbstractWorkflowEvent implements WorkflowEventContract
{
    use ValidatesInputTokens;
    use GeneratesNameAndAliases;

    public function getQueue(): string
    {
        return config('workflowable.queue');
    }
}
