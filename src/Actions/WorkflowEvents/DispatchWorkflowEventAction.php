<?php

namespace Workflowable\Workflow\Actions\WorkflowEvents;

use Illuminate\Support\Collection;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Models\Workflow;

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
