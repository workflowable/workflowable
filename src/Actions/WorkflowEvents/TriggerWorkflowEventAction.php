<?php

namespace Workflowable\Workflowable\Actions\WorkflowEvents;

use Illuminate\Support\Collection;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Actions\WorkflowProcesses\CanDispatchWorkflowProcessAction;
use Workflowable\Workflowable\Actions\WorkflowProcesses\CreateWorkflowProcessAction;
use Workflowable\Workflowable\Actions\WorkflowProcesses\DispatchWorkflowProcessAction;
use Workflowable\Workflowable\Models\Workflow;

/**
 * Takes a workflow event and triggers all workflows that are active and have a workflow event that matches the
 * event that was triggered
 */
class TriggerWorkflowEventAction extends AbstractAction
{
    public function handle(AbstractWorkflowEvent $workflowEvent): Collection
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
                $workflowProcess = CreateWorkflowProcessAction::make()
                    ->handle($workflow, $workflowEvent);

                if (CanDispatchWorkflowProcessAction::make()->handle($workflowProcess)) {
                    // Dispatch the run so that it can be processed
                    DispatchWorkflowProcessAction::make()->handle($workflowProcess, $workflowEvent->getQueue());
                }

                // Identify that the workflow run was spawned by the triggering of the event
                $workflowProcessCollection->push($workflowProcess);
            });

        // Return all the workflow runs that were spawned by the triggering of the event
        return $workflowProcessCollection;
    }
}
