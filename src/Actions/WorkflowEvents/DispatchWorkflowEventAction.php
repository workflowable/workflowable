<?php

namespace Workflowable\Workflow\Actions\WorkflowEvents;

use Illuminate\Support\Collection;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Contracts\WorkflowEventManagerContract;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCreated;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunDispatched;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Jobs\WorkflowRunnerJob;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowStatus;

class DispatchWorkflowEventAction
{
    /**
     * Trigger an event and return all the workflow runs that were spawned by the event
     *
     *
     *
     * @throws WorkflowEventException
     */
    public function handle(WorkflowEventContract $workflowEvent): Collection
    {
        // track the workflow runs that we are going to be dispatching
        $workflowRunCollection = collect();

        /** @var WorkflowEventManagerContract $eventManager */
        $eventManager = app(WorkflowEventManagerContract::class);

        if (! $eventManager->isRegistered($workflowEvent->getAlias())) {
            throw WorkflowEventException::workflowEventNotRegistered($workflowEvent);
        }

        $isValid = $eventManager->isValid($workflowEvent->getAlias(), get_object_vars($workflowEvent));

        if (! $isValid) {
            throw WorkflowEventException::invalidWorkflowEventParameters();
        }

        /**
         * Find all workflows that are active and have a workflow event that matches the event that was triggered
         */
        Workflow::query()
            ->whereHas('workflowEvent', function ($query) use ($workflowEvent) {
                $query->where('alias', $workflowEvent->getAlias());
            })
            ->where('workflow_status_id', WorkflowStatus::ACTIVE)
            ->each(function (Workflow $workflow) use (&$workflowRunCollection, $workflowEvent) {
                // Create the workflow run and identify it as having been created
                $workflowRun = new WorkflowRun();
                $workflowRun->workflow()->associate($workflow);
                $workflowRun->workflowRunStatus()->associate(WorkflowRunStatus::CREATED);
                $workflowRun->parameters = get_object_vars($workflowEvent);
                $workflowRun->save();

                // Alert the system of the creation of a workflow run being created
                WorkflowRunCreated::dispatch($workflowRun);

                // Identify the workflow run as being dispatched
                $workflowRun->workflow_run_status_id = WorkflowRunStatus::DISPATCHED;
                $workflowRun->save();

                // Dispatch the workflow run
                WorkflowRunDispatched::dispatch($workflowRun);
                WorkflowRunnerJob::dispatch($workflowRun, $workflowEvent);

                // Add the workflow run to the collection
                $workflowRunCollection->push($workflowRun);
            });

        // Return all the workflow runs that were spawned by the triggering of the event
        return $workflowRunCollection;
    }
}
