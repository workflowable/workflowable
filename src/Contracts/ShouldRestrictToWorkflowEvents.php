<?php

namespace Workflowable\Workflowable\Contracts;

/**
 * Interface ShouldScopeToWorkflowEvents
 *
 * TODO: Replace this with new fields paradigm at a future date
 */
interface ShouldRestrictToWorkflowEvents
{
    public function getRestrictedWorkflowEventClasses(): array;
}
