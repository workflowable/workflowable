<?php

namespace Workflowable\Workflowable\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Models\WorkflowRun;

/**
 * @method static Collection  triggerEvent(WorkflowEventContract $abstractWorkflowEvent)
 * @method static WorkflowRun cancelRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun pauseRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun resumeRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun dispatchRun(WorkflowRun $workflowRun, string $queue)
 * @method static WorkflowRun createWorkflowRun(Workflow $workflow, WorkflowEventContract $workflowEvent)
 * @method static Workflow    createWorkflow(string $name, WorkflowEvent|int $workflowEvent, WorkflowPriority|int $workflowPriority, int $retryInterval)
 * @method static Workflow    activateWorkflow(Workflow $workflow)
 * @method static Workflow    archiveWorkflow(Workflow $workflow)
 * @method static Workflow    deactivateWorkflow(Workflow $workflow)
 * @method static Workflow    cloneWorkflow(Workflow $workflow, string $newWorkflowName)
 * @method static Workflow    swap(Workflow $workflowToDeactivate, Workflow $workflowToActivate))
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
