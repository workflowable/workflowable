<?php

namespace Workflowable\Workflowable\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowRun;

/**
 * @method static Collection triggerEvent(WorkflowEventContract $abstractWorkflowEvent)
 * @method static WorkflowRun cancelRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun pauseRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun resumeRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun dispatchRun(WorkflowRun $workflowRun, string $queue)
 * @method static WorkflowRun createWorkflowRun(Workflow $workflow, WorkflowEventContract $workflowEvent)
 */
class Workflowable extends Facade
{
    /**
     * Identifies the default manager for the facade class
     */
    protected static function getFacadeAccessor(): string
    {
        return \Workflowable\Workflowable\Workflowable::class;
    }
}
