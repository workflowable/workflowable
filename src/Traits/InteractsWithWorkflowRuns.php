<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Support\Collection;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunCancelled;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunCreated;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunDispatched;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunPaused;
use Workflowable\Workflowable\Events\WorkflowRuns\WorkflowRunResumed;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Jobs\WorkflowRunnerJob;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunStatus;
use Workflowable\Workflowable\Models\WorkflowRunToken;

trait InteractsWithWorkflowRuns
{
    /**
     * Takes a workflow event and triggers all workflows that are active and have a workflow event that matches the
     * event that was triggered
     */
    public function triggerEvent(AbstractWorkflowEvent $workflowEvent): Collection
    {
        // track the workflow runs that we are going to be dispatching
        $workflowRunCollection = collect();

        /**
         * Find all workflows that are active and have a workflow event that matches the event that was triggered
         */
        Workflow::query()
            ->active()
            ->forEvent($workflowEvent)
            ->each(function (Workflow $workflow) use (&$workflowRunCollection, $workflowEvent) {
                // Create the run
                $workflowRun = $this->createWorkflowRun($workflow, $workflowEvent);

                // Dispatch the run so that it can be processed
                $this->dispatchRun($workflowRun, $workflowEvent->getQueue());

                // Identify that the workflow run was spawned by the triggering of the event
                $workflowRunCollection->push($workflowRun);
            });

        // Return all the workflow runs that were spawned by the triggering of the event
        return $workflowRunCollection;
    }

    /**
     * Creates a workflow run
     *
     * @throws WorkflowEventException
     */
    public function createWorkflowRun(Workflow $workflow, AbstractWorkflowEvent $workflowEvent): WorkflowRun
    {
        $isValid = $workflowEvent->hasValidParameters();

        if (! $isValid) {
            throw WorkflowEventException::invalidWorkflowEventParameters();
        }

        // Create the workflow run and identify it as having been created
        $workflowRun = new WorkflowRun();
        $workflowRun->workflow()->associate($workflow);
        $workflowRun->workflowRunStatus()->associate(WorkflowRunStatus::CREATED);
        $workflowRun->save();

        // Create the workflow run parameters
        foreach ($workflowEvent->getParameters() as $key => $value) {
            $this->createInputParameter($workflowRun, $key, $value);
        }

        // Alert the system of the creation of a workflow run being created
        WorkflowRunCreated::dispatch($workflowRun);

        return $workflowRun;
    }

    /**
     * Dispatches a workflow run so that it can be picked up by the workflow runner
     */
    public function dispatchRun(WorkflowRun $workflowRun, string $queue = 'default'): WorkflowRun
    {
        // Identify the workflow run as being dispatched
        $workflowRun->workflow_run_status_id = WorkflowRunStatus::DISPATCHED;
        $workflowRun->save();

        // Dispatch the workflow run
        WorkflowRunnerJob::dispatch($workflowRun)->onQueue($queue);
        WorkflowRunDispatched::dispatch($workflowRun);

        return $workflowRun;
    }

    /**
     * Pauses a workflow run so that it won't be picked up by the workflow runner
     *
     * @throws \Exception
     */
    public function pauseRun(WorkflowRun $workflowRun): WorkflowRun
    {
        if ($workflowRun->workflow_run_status_id != WorkflowRunStatus::PENDING) {
            throw new \Exception('Workflow run is not pending');
        }

        $workflowRun->workflow_run_status_id = WorkflowRunStatus::PAUSED;
        $workflowRun->save();

        WorkflowRunPaused::dispatch($workflowRun);

        return $workflowRun;
    }

    /**
     * Resumes a workflow run so that it can be picked up by the workflow runner
     *
     * @throws \Exception
     */
    public function resumeRun(WorkflowRun $workflowRun): WorkflowRun
    {
        if ($workflowRun->workflow_run_status_id != WorkflowRunStatus::PAUSED) {
            throw new \Exception('Workflow run is not paused');
        }

        $workflowRun->workflow_run_status_id = WorkflowRunStatus::PENDING;
        $workflowRun->save();

        WorkflowRunResumed::dispatch($workflowRun);

        return $workflowRun;
    }

    /**
     * Cancels a workflow run so that it won't be picked up by the workflow runner
     *
     * @throws \Exception
     */
    public function cancelRun(WorkflowRun $workflowRun): WorkflowRun
    {
        if ($workflowRun->workflow_run_status_id != WorkflowRunStatus::PENDING) {
            throw new \Exception('Workflow run is not pending');
        }

        $workflowRun->workflow_run_status_id = WorkflowRunStatus::CANCELLED;
        $workflowRun->save();

        WorkflowRunCancelled::dispatch($workflowRun);

        return $workflowRun;
    }

    /**
     * Creates an input parameter for the workflow run
     */
    public function createInputParameter(WorkflowRun $workflowRun, string $key, mixed $value): WorkflowRunToken
    {
        /** @var WorkflowRunToken $workflowRunParameter */
        $workflowRunParameter = $workflowRun->workflowRunTokens()->create([
            'workflow_activity_id' => null,
            'key' => $key,
            'value' => $value,
        ]);

        return $workflowRunParameter;
    }

    /**
     * Creates an output parameter for the workflow run and identifies the step that created it
     */
    public function createOutputParameter(WorkflowRun $workflowRun, WorkflowActivity $workflowActivity, string $key, mixed $value): WorkflowRunToken
    {
        /** @var WorkflowRunToken $workflowRunParameter */
        $workflowRunParameter = $workflowRun->workflowRunTokens()->create([
            'workflow_activity_id' => $workflowActivity->id,
            'key' => $key,
            'value' => $value,
        ]);

        return $workflowRunParameter;
    }
}
