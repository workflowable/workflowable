<?php

namespace Workflowable\Workflowable\Actions\WorkflowSwaps;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\Conditionable;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowProcesses\CancelWorkflowProcessAction;
use Workflowable\Workflowable\Actions\WorkflowProcesses\CreateWorkflowProcessAction;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Models\WorkflowSwapAuditLog;

class SwapWorkflowProcessAction extends AbstractAction
{
    use Conditionable;

    public function handle(WorkflowSwap $workflowSwap, WorkflowProcess $existingWorkflowProcess): WorkflowSwapAuditLog
    {
        return DB::transaction(function () use ($workflowSwap, $existingWorkflowProcess) {
            // Cancel the existing workflow process so no remaining activities will be performed
            CancelWorkflowProcessAction::make()->handle($existingWorkflowProcess);

            $newWorkflowProcess = $this->handleWorkflowProcessMigration($workflowSwap, $existingWorkflowProcess);

            if ($workflowSwap->should_transfer_output_tokens) {
                $this->handleOutputTokenMigration($workflowSwap, $existingWorkflowProcess, $newWorkflowProcess);
            }

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

    /**
     * @throws WorkflowSwapException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Workflowable\Workflowable\Exceptions\WorkflowEventException
     */
    public function handleWorkflowProcessMigration(WorkflowSwap $workflowSwap, WorkflowProcess $existingWorkflowProcess): WorkflowProcess
    {
        // Get the activity map for the current
        $activityMap = $workflowSwap->workflowSwapActivityMaps
            ->where('from_workflow_activity_id', $existingWorkflowProcess->last_workflow_activity_id)
            ->first();

        if (empty($activityMap)) {
            throw WorkflowSwapException::missingWorkflowSwapActivityMap();
        }

        // Create a new workflow process using the same tokens
        $createWorkflowProcess = CreateWorkflowProcessAction::make();

        $this->when(! empty($activityMap->to_workflow_activity_id), function () use ($createWorkflowProcess, $activityMap) {
            $createWorkflowProcess->withLastWorkflowActivity($activityMap->toWorkflowActivity);
        });

        $inputTokens = $existingWorkflowProcess->workflowProcessTokens
            ->whereNull('workflow_activity_id')
            ->mapWithKeys(function (WorkflowProcessToken $token) {
                return [$token->key => $token->value];
            });

        $workflowEvent = new $existingWorkflowProcess->workflow->workflowEvent->class_name($inputTokens->toArray());

        return $createWorkflowProcess
            ->withNextRunAt($existingWorkflowProcess->next_run_at)
            ->handle($workflowSwap->toWorkflow, $workflowEvent);
    }

    protected function handleOutputTokenMigration(WorkflowSwap $workflowSwap, WorkflowProcess $existingWorkflowProcess, WorkflowProcess $newWorkflowProcess): void
    {
        $outputTokens = $existingWorkflowProcess->workflowProcessTokens
            ->whereNotNull('workflow_activity_id')
            // Reject tokens that belong to an activity that will not be ported over
            ->reject(function (WorkflowProcessToken $processToken) use ($workflowSwap) {
                $activityMap = $workflowSwap->workflowSwapActivityMaps
                    ->where('from_workflow_activity_id', $processToken->workflow_activity_id)
                    ->first();

                if (is_null($activityMap)) {
                    return true;
                }

                return is_null($activityMap->to_workflow_activity_id);
            })
            // Map tokens belonging to
            ->map(function (WorkflowProcessToken $processToken) use ($workflowSwap, $newWorkflowProcess) {
                $activityMap = $workflowSwap->workflowSwapActivityMaps
                    ->where('from_workflow_activity_id', $processToken->workflow_activity_id)
                    ->first();

                return [
                    'workflow_process_id' => $newWorkflowProcess->id,
                    'workflow_activity_id' => $activityMap->to_workflow_activity_id,
                    'key' => $processToken->key,
                    'value' => $processToken->value,
                ];
            })->toArray();

        WorkflowProcessToken::query()->insert($outputTokens);
    }
}
