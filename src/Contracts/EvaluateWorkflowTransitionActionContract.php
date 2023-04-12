<?php

namespace Workflowable\Workflow\Contracts;

use Workflowable\Workflow\Models\WorkflowTransition;

interface EvaluateWorkflowTransitionActionContract
{
    public function handle(WorkflowTransition $workflowTransition): bool;
}
