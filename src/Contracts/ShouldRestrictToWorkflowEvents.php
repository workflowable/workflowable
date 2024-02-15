<?php

namespace Workflowable\Workflowable\Contracts;

interface ShouldRestrictToWorkflowEvents
{
    public function getRestrictedWorkflowEventClasses(): array;
}
