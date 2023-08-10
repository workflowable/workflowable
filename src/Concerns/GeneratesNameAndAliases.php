<?php

namespace Workflowable\Workflowable\Concerns;

use Illuminate\Support\Str;

trait GeneratesNameAndAliases
{
    /**
     * Generates a unique alias for the workflow data structure.
     */
    public function getAlias(): string
    {
        return Str::of(static::class)->classBasename()->snake()->toString();
    }

    /**
     * Generate a human-readable name for the workflow data structure.
     */
    public function getName(): string
    {
        return Str::of(static::class)->classBasename()->headline()->toString();
    }
}
