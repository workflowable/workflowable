<?php

namespace Workflowable\Workflowable\Actions\Workflows;

use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\Workflows\WorkflowDeactivated;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;

class DeactivateWorkflowAction extends AbstractAction
{
    public function handle(Workflow $workflow): Workflow
    {
        DB::transaction(function () use ($workflow) {
            if ($workflow->workflow_status_id === WorkflowStatusEnum::DEACTIVATED) {
                throw WorkflowException::workflowAlreadyDeactivated();
            }

            $workflow->workflow_status_id = WorkflowStatusEnum::DEACTIVATED;
            $workflow->save();

            WorkflowDeactivated::dispatch($workflow);
        });

        return $workflow;
    }
}
