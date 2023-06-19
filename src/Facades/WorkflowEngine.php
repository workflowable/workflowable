<?php

namespace Workflowable\Workflow\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Workflowable\Workflow\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflow\Managers\WorkflowEngineManager;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowRun;

/**
 * @method static Collection triggerEvent(AbstractWorkflowEvent $abstractWorkflowEvent)
 * @method static WorkflowRun dispatchWorkflow(Workflow $workflow, AbstractWorkflowEvent $abstractWorkflowEvent)
 */
class WorkflowEngine extends Facade
{
    /**
     * Identifies the default manager for the facade class
     */
    protected static function getFacadeAccessor(): string
    {
        return WorkflowEngineManager::class;
    }
}
