<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Support\Collection;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCancelled;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCreated;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessDispatched;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessPaused;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessResumed;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Jobs\WorkflowProcessRunnerJob;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessStatus;
use Workflowable\Workflowable\Models\WorkflowProcessToken;

trait InteractsWithWorkflowProcesses
{
    /**
     * Takes a workflow event and triggers all workflows that are active and have a workflow event that matches the
     * event that was triggered
     */
    public function triggerEvent(AbstractWorkflowEvent $workflowEvent): Collection
    {
        // track the workflow runs that we are going to be dispatching
        $workflowProcessCollection = collect();

        /**
         * Find all workflows that are active and have a workflow event that matches the event that was triggered
         */
        Workflow::query()
            ->active()
            ->forEvent($workflowEvent)
            ->each(function (Workflow $workflow) use (&$workflowProcessCollection, $workflowEvent) {
                // Create the run
                $workflowProcess = $this->createWorkflowRun($workflow, $workflowEvent);

                // Dispatch the run so that it can be processed
                $this->dispatchRun($workflowProcess, $workflowEvent->getQueue());

                // Identify that the workflow run was spawned by the triggering of the event
                $workflowProcessCollection->push($workflowProcess);
            });

        // Return all the workflow runs that were spawned by the triggering of the event
        return $workflowProcessCollection;
    }

    /**
     * Creates a workflow run
     *
     * @throws WorkflowEventException
     */
    public function createWorkflowRun(Workflow $workflow, AbstractWorkflowEvent $workflowEvent): WorkflowProcess
    {
        $isValid = $workflowEvent->hasValidTokens();

        if (! $isValid) {
            throw WorkflowEventException::invalidWorkflowEventParameters();
        }

        // Create the workflow run and identify it as having been created
        $workflowProcess = new WorkflowProcess();
        $workflowProcess->workflow()->associate($workflow);
        $workflowProcess->workflowProcessStatus()->associate(WorkflowProcessStatus::CREATED);
        $workflowProcess->save();

        // Create the workflow run parameters
        foreach ($workflowEvent->getTokens() as $key => $value) {
            $this->createInputToken($workflowProcess, $key, $value);
        }

        // Alert the system of the creation of a workflow run being created
        WorkflowProcessCreated::dispatch($workflowProcess);

        return $workflowProcess;
    }

    /**
     * Dispatches a workflow run so that it can be picked up by the workflow runner
     */
    public function dispatchRun(WorkflowProcess $workflowProcess, string $queue = 'default'): WorkflowProcess
    {
        // Identify the workflow run as being dispatched
        $workflowProcess->workflow_process_status_id = WorkflowProcessStatus::DISPATCHED;
        $workflowProcess->save();

        // Dispatch the workflow run
        WorkflowProcessRunnerJob::dispatch($workflowProcess)->onQueue($queue);
        WorkflowProcessDispatched::dispatch($workflowProcess);

        return $workflowProcess;
    }

    /**
     * Pauses a workflow run so that it won't be picked up by the workflow runner
     *
     * @throws \Exception
     */
    public function pauseRun(WorkflowProcess $workflowProcess): WorkflowProcess
    {
        if ($workflowProcess->workflow_process_status_id != WorkflowProcessStatus::PENDING) {
            throw new \Exception('Workflow run is not pending');
        }

        $workflowProcess->workflow_process_status_id = WorkflowProcessStatus::PAUSED;
        $workflowProcess->save();

        WorkflowProcessPaused::dispatch($workflowProcess);

        return $workflowProcess;
    }

    /**
     * Resumes a workflow run so that it can be picked up by the workflow runner
     *
     * @throws \Exception
     */
    public function resumeRun(WorkflowProcess $workflowProcess): WorkflowProcess
    {
        if ($workflowProcess->workflow_process_status_id != WorkflowProcessStatus::PAUSED) {
            throw new \Exception('Workflow run is not paused');
        }

        $workflowProcess->workflow_process_status_id = WorkflowProcessStatus::PENDING;
        $workflowProcess->save();

        WorkflowProcessResumed::dispatch($workflowProcess);

        return $workflowProcess;
    }

    /**
     * Cancels a workflow run so that it won't be picked up by the workflow runner
     *
     * @throws \Exception
     */
    public function cancelRun(WorkflowProcess $workflowProcess): WorkflowProcess
    {
        if ($workflowProcess->workflow_process_status_id != WorkflowProcessStatus::PENDING) {
            throw new \Exception('Workflow run is not pending');
        }

        $workflowProcess->workflow_process_status_id = WorkflowProcessStatus::CANCELLED;
        $workflowProcess->save();

        WorkflowProcessCancelled::dispatch($workflowProcess);

        return $workflowProcess;
    }

    /**
     * Creates an input parameter for the workflow run
     */
    public function createInputToken(WorkflowProcess $workflowProcess, string $key, mixed $value): WorkflowProcessToken
    {
        /** @var WorkflowProcessToken $workflowProcessToken */
        $workflowProcessToken = $workflowProcess->workflowProcessTokens()->create([
            'workflow_activity_id' => null,
            'key' => $key,
            'value' => $value,
        ]);

        return $workflowProcessToken;
    }

    /**
     * Creates an output token for the workflow run and identifies the activity that created it
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
