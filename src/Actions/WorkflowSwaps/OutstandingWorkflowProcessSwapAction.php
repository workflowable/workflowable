<?php

namespace Workflowable\Workflowable\Actions\WorkflowSwaps;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\Conditionable;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\Workflowable\Actions\WorkflowProcesses\CancelWorkflowProcessAction;
use Workflowable\Workflowable\Actions\WorkflowProcesses\CreateWorkflowProcessAction;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Models\WorkflowSwapAuditLog;

class OutstandingWorkflowProcessSwapAction extends AbstractAction
{
    use Conditionable;

    public function handle(WorkflowSwap $workflowSwap, WorkflowProcess $existingWorkflowProcess): WorkflowSwapAuditLog
    {
        return DB::transaction(function () use ($workflowSwap, $existingWorkflowProcess) {
            // Cancel the existing workflow process so no remaining activities will be performed
            CancelWorkflowProcessAction::make()->handle($existingWorkflowProcess);

            $activityMap = $workflowSwap->workflowSwapActivityMaps
                ->where('from_workflow_activity_id', $existingWorkflowProcess->last_workflow_activity_id)
                ->first();

            // Create a new workflow process using the same tokens
            $createWorkflowProcess = CreateWorkflowProcessAction::make();

            $this->when(! empty($activityMap->to_workflow_activity_id), function () use ($createWorkflowProcess, $activityMap) {
                $createWorkflowProcess->withLastWorkflowActivity($activityMap->toWorkflowActivity);
            });

            $inputTokens = $existingWorkflowProcess->workflowProcessTokens
                ->whereNull('workflow_activity_id')
                ->map(function (WorkflowProcessToken $token) {
                    return [
                        'key' => $token->key,
                        'value' => $token->value,
                    ];
                });

            $workflowEvent = GetWorkflowEventImplementationAction::make()
                ->handle($existingWorkflowProcess->workflow->workflow_event_id, $inputTokens);

            $newWorkflowProcess = $createWorkflowProcess
                ->withNextRunAt($existingWorkflowProcess->next_run_at)
                ->handle($workflowSwap->toWorkflow, $workflowEvent);

            /**
             * Create the audit record to indicate that we touched both workflow processes, and identify the
             * impacted activities
             *
             * @var WorkflowSwapAuditLog $workflowSwapAuditLog
             */
            $workflowSwapAuditLog = WorkflowSwapAuditLog::query()->create([
                'workflow_swap_id' => $workflowSwap->id,
                'from_workflow_process_id' => $existingWorkflowProcess->id,
                'from_workflow_activity_id' => $existingWorkflowProcess->last_workflow_activity_id,
                'to_workflow_process_id' => $newWorkflowProcess->id,
                'to_workflow_activity_id' => $newWorkflowProcess->last_workflow_activity_id,
            ]);

            return $workflowSwapAuditLog;
        });
    }
}
