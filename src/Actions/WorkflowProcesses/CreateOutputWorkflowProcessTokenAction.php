<?php

namespace Workflowable\Workflowable\Actions\WorkflowProcesses;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;

class CreateOutputWorkflowProcessTokenAction extends AbstractAction
{
    /**
     * Creates an output token for the workflow process and identifies the activity that created it
     */
    public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity, string $key, mixed $value): WorkflowProcessToken
    {
        /** @var WorkflowProcessToken $workflowProcessToken */
        $workflowProcessToken = $workflowProcess->workflowProcessTokens()->create([
            'workflow_activity_id' => $workflowActivity->id,
            'key' => $key,
            'value' => $value,
        ]);

        return $workflowProcessToken;
    }
}
