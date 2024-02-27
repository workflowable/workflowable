<?php

namespace Workflowable\Workflowable\Concerns;

use Illuminate\Support\Str;

trait GeneratesHumanReadableNameForClass
{
    /**
     * Generate a human-readable name for the workflow data structure.
     */
    public function getName(): string
    {
        return Str::of(static::class)->classBasename()->headline()->toString();
    }
}
