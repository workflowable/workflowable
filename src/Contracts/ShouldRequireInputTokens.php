<?php

namespace Workflowable\Workflowable\Contracts;

interface ShouldRequireInputTokens
{
    public function getRequiredWorkflowEventTokenKeys(): array;
}
