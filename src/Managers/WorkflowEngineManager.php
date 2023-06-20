<?php

namespace Workflowable\Workflow\Managers;

use Illuminate\Support\Collection;
use Workflowable\Workflow\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCancelled;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCreated;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunDispatched;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunPaused;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunResumed;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Jobs\WorkflowRunnerJob;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;

class WorkflowEngineManager
{
    /**
     * Takes a workflow event and triggers all workflows that are active and have a workflow event that matches the
     * event that was triggered
     *
     * @param AbstractWorkflowEvent $workflowEvent
     * @return Collection
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
                $this->dispatchRun($workflowRun);

                // Identify that the workflow run was spawned by the triggering of the event
                $workflowRunCollection->push($workflowRun);
            });

        // Return all the workflow runs that were spawned by the triggering of the event
        return $workflowRunCollection;
    }

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
        foreach ($workflowEvent->getParameters() as $name => $value) {
            $workflowRun->workflowRunParameters()->create([
                'name' => $name,
                'value' => $value,
            ]);
        }

        // Alert the system of the creation of a workflow run being created
        WorkflowRunCreated::dispatch($workflowRun);

        return $workflowRun;
    }

    /**
     * Dispatches a workflow run
     *
     * @param WorkflowRun $workflowRun
     *
     * @return WorkflowRun
     **/
    public function dispatchRun(WorkflowRun $workflowRun): WorkflowRun
    {
        // Identify the workflow run as being dispatched
        $workflowRun->workflow_run_status_id = WorkflowRunStatus::DISPATCHED;
        $workflowRun->save();

        // Dispatch the workflow run
        WorkflowRunDispatched::dispatch($workflowRun);
        WorkflowRunnerJob::dispatch($workflowRun);

        return $workflowRun;
    }

    /**
     * Pauses a workflow run so that it won't be picked up by the workflow runner
     *
     * @param WorkflowRun $workflowRun
     * @return WorkflowRun
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
     * @param WorkflowRun $workflowRun
     * @return WorkflowRun
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
     * @param WorkflowRun $workflowRun
     * @return WorkflowRun
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
}
