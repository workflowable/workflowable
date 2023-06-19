<?php

namespace Workflowable\Workflow\Actions\WorkflowEvents;

use Illuminate\Support\Collection;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
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

    }
}
