<?php

namespace Workflowable\Workflow\Contracts;

use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowTransition;

interface EvaluateWorkflowTransitionActionContract
{
    public function handle(WorkflowRun $workflowRun, WorkflowTransition $workflowTransition): bool;
}
