<?php

namespace Workflowable\Workflowable\Actions\WorkflowProcesses;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;

class CreateInputWorkflowProcessTokenAction extends AbstractAction
{
    /**
     * Creates an output token for the workflow process and identifies the activity that created it
     */
    public function handle(WorkflowProcess $workflowProcess, string $key, mixed $value): WorkflowProcessToken
    {
        /** @var WorkflowProcessToken $workflowProcessToken */
        $workflowProcessToken = $workflowProcess->workflowProcessTokens()->create([
            'workflow_activity_id' => null,
            'key' => $key,
            'value' => $value,
        ]);

        return $workflowProcessToken;
    }
}
