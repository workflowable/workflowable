<?php

namespace Workflowable\WorkflowEngine\Actions\Workflows;

use Workflowable\WorkflowEngine\Models\Workflow;

class CloneWorkflowAction
{
    public function handle(Workflow $workflow, string $newWorkflowName): Workflow
    {
        $newWorkflow = $workflow->replicate();
        $newWorkflow->name = $newWorkflowName;
        $newWorkflow->save();

        return $newWorkflow;
    }
}
