<?php

namespace Workflowable\Workflow\Managers;

use Illuminate\Support\Collection;
use Workflowable\Workflow\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunCreated;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunDispatched;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Jobs\WorkflowRunnerJob;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;

class WorkflowEngineManager
{
    /**
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
                $workflowRunCollection->push(
                    $this->dispatchWorkflow($workflow, $workflowEvent)
                );
            });

        // Return all the workflow runs that were spawned by the triggering of the event
        return $workflowRunCollection;
    }

    /**
     * @param Workflow $workflow
     * @param AbstractWorkflowEvent $workflowEvent
     * @return WorkflowRun
     * @throws WorkflowEventException
     */
    public function dispatchWorkflow(Workflow $workflow, AbstractWorkflowEvent $workflowEvent): WorkflowRun
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

        foreach ($workflowEvent->getParameters() as $name => $value) {
            $workflowRun->workflowRunParameters()->create([
                'name' => $name,
                'value' => $value,
            ]);
        }

        // Alert the system of the creation of a workflow run being created
        WorkflowRunCreated::dispatch($workflowRun);

        // Identify the workflow run as being dispatched
        $workflowRun->workflow_run_status_id = WorkflowRunStatus::DISPATCHED;
        $workflowRun->save();

        // Dispatch the workflow run
        WorkflowRunDispatched::dispatch($workflowRun);
        WorkflowRunnerJob::dispatch($workflowRun);

        return $workflowRun;
    }
}
