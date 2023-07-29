<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowRun;

/**
 * Finds the next activity for the workflow run by ordering the transitions by ordinal and evaluating them until one passes
 */
interface GetNextActivityForWorkflowRunActionContract
{
    public function handle(WorkflowRun $workflowRun): ?WorkflowActivity;
}
