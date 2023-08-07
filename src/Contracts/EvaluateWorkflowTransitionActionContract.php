<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowTransition;

interface EvaluateWorkflowTransitionActionContract
{
    public function handle(WorkflowProcess $workflowProcess, WorkflowTransition $workflowTransition): bool;
}
