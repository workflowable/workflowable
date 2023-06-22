<?php

namespace Workflowable\WorkflowEngine\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Workflowable\WorkflowEngine\Abstracts\AbstractWorkflowEvent;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowRun;

/**
 * @method static Collection triggerEvent(AbstractWorkflowEvent $abstractWorkflowEvent)
 * @method static WorkflowRun dispatchWorkflow(Workflow $workflow, AbstractWorkflowEvent $abstractWorkflowEvent)
 * @method static WorkflowRun cancelRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun pauseRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun resumeRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun dispatchRun(WorkflowRun $workflowRun)
 */
class WorkflowEngine extends Facade
{
    /**
     * Identifies the default manager for the facade class
     */
    protected static function getFacadeAccessor(): string
    {
        return \Workflowable\WorkflowEngine\WorkflowEngine::class;
    }
}
