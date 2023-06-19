<?php

namespace Workflowable\Workflow\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Workflowable\Workflow\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflow\Managers\WorkflowEngineManager;

/**
 * @method static Collection triggerEvent(AbstractWorkflowEvent $abstractWorkflowEvent)
 */
class WorkflowEngine extends Facade
{
    /**
     * Identifies the default manager for the facade class
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return WorkflowEngineManager::class;
    }
}
