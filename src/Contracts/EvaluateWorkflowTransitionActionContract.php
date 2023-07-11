<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowTransition;

interface EvaluateWorkflowTransitionActionContract
{
    public function handle(WorkflowRun $workflowRun, WorkflowTransition $workflowTransition): bool;
}
