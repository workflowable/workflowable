<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowStep;

/**
 * Finds the next step for the workflow run by ordering the transitions by ordinal and evaluating them until one passes
 */
interface GetNextStepForWorkflowRunActionContract
{
    public function handle(WorkflowRun $workflowRun): ?WorkflowStep;
}
