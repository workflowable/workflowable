<?php

namespace Workflowable\WorkflowEngine\Contracts;

use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;

interface EvaluateWorkflowTransitionActionContract
{
    public function handle(WorkflowRun $workflowRun, WorkflowTransition $workflowTransition): bool;
}
