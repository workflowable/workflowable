<?php

namespace Workflowable\Workflowable\Abstracts;

use Illuminate\Support\Str;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Traits\InteractsWithWorkflowProcesses;
use Workflowable\Workflowable\Traits\ValidatesWorkflowParameters;

abstract class AbstractWorkflowActivityType implements WorkflowActivityTypeContract
{
    use ValidatesWorkflowParameters;
    use InteractsWithWorkflowProcesses;

    /**
     * {@inheritDoc}
     */
    public function getAlias(): string
    {
        return Str::of(static::class)->classBasename()->snake()->toString();
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return Str::of(static::class)->classBasename()->headline()->toString();
    }
}
