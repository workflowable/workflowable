<?php

namespace Workflowable\Workflowable\Managers;

use Illuminate\Database\Eloquent\Builder;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessDispatched;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Jobs\WorkflowProcessRunnerJob;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;
use Workflowable\Workflowable\Models\WorkflowSwap;

class WorkflowableManager
{
    public function canDispatchWorkflowProcess(WorkflowProcess $workflowProcess): bool
    {
        return ! $this->hasWorkflowSwapInProcess($workflowProcess);
    }

    public function hasWorkflowSwapInProcess(WorkflowProcess $workflowProcess): bool
    {
        return WorkflowSwap::query()
            ->where(function (Builder $query) use ($workflowProcess) {
                $query->where('from_workflow_id', $workflowProcess->workflow_id)
                    ->orWhere('to_workflow_id', $workflowProcess->workflow_id);
            })
            ->whereIn('workflow_swap_status_id', [
                WorkflowSwapStatusEnum::Dispatched,
                WorkflowSwapStatusEnum::Processing,
            ])->exists();
    }

    /**
     * Dispatches a workflow process so that it can be picked up by the workflow process runner
     *
     * @throws WorkflowSwapException
     */
    public function dispatchProcess(WorkflowProcess $workflowProcess, string $queue = 'default'): WorkflowProcess
    {
        if ($this->hasWorkflowSwapInProcess($workflowProcess)) {
            throw WorkflowSwapException::workflowSwapInProcess();
        }

        // Identify the workflow run as being dispatched
        $workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::DISPATCHED;
        $workflowProcess->save();

        // Dispatch the workflow run
        WorkflowProcessRunnerJob::dispatch($workflowProcess)->onQueue($queue);
        WorkflowProcessDispatched::dispatch($workflowProcess);

        return $workflowProcess;
    }

    /**
     * Creates an output token for the workflow process and identifies the activity that created it
     */
    public function createOutputToken(WorkflowProcess $workflowRun, WorkflowActivity $workflowActivity, string $key, mixed $value): WorkflowProcessToken
    {
        /** @var WorkflowProcessToken $workflowRunToken */
        $workflowRunToken = $workflowRun->workflowProcessTokens()->create([
            'workflow_activity_id' => $workflowActivity->id,
            'key' => $key,
            'value' => $value,
        ]);

        return $workflowRunToken;
    }
}
