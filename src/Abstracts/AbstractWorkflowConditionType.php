<?php

namespace Workflowable\Workflowable\Abstracts;

use Illuminate\Support\Str;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Traits\ValidatesWorkflowParameters;

abstract class AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    use ValidatesWorkflowParameters;

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
