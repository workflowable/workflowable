<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

/**
 * Finds the next activity for the workflow process by ordering the transitions by ordinal and evaluating them until one passes
 */
interface GetNextActivityForWorkflowProcessActionContract
{
    public function handle(WorkflowProcess $workflowRun): ?WorkflowActivity;
}
