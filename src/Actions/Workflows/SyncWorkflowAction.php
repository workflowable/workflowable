<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Workflowable\Workflow\Models\Workflow;

class SyncWorkflowAction
{
    public function handle(Workflow|int $workflow): Workflow
    {
        if (is_int($workflow)) {
            $workflow = Workflow::query()->findOrFail($workflow);
        }

        // Look at workflow step and see if it has been created by looking at UX defined uuid
        // If it has been created, then update the workflow step
        // If it has not been created, then create the workflow step
        // If it has been deleted, then delete the workflow step

        // Look at workflow transition and see if it has been created by looking at UX defined uuid
        // If it has been created, then update the workflow transition
        // If it has not been created, then create the workflow transition
        // If it has been deleted, then delete the workflow transition

        // Look at workflow condition and see if it has been created by looking at UX defined uuid
        // If it has been created, then update the workflow condition
        // If it has not been created, then create the workflow condition
        // If it has been deleted, then delete the workflow condition

        return $workflow;
    }
}
