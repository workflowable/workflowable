<?php

namespace Workflowable\WorkflowEngine\Actions\Workflows;

use Illuminate\Support\Facades\DB;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;

class SwapActiveWorkflowAction
{
    public function handle(Workflow $workflowToDeactivate, Workflow $workflowToActivate): Workflow
    {
        DB::statement('
            UPDATE `workflows`
                SET `workflow_status_id` = CASE
                    WHEN `id` = ? THEN ?
                    WHEN `id` = ? THEN ?
                    ELSE `workflow_status_id`
                END
                WHERE `id` IN (?, ?)
            ', [
            $workflowToDeactivate->id,
            WorkflowStatus::INACTIVE,
            $workflowToActivate->id,
            WorkflowStatus::ACTIVE,
            $workflowToDeactivate->id,
            $workflowToActivate->id,
        ]);

        $workflowToActivate->refresh();

        return $workflowToActivate;
    }
}
