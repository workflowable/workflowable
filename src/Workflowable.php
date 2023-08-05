<?php

namespace Workflowable\Workflowable;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Managers\WorkflowableManager;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowPriority;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunToken;

/**
 * @method static Collection triggerEvent(AbstractWorkflowEvent $workflowEvent)
 * @method static WorkflowRun createWorkflowRun(Workflow $workflow, AbstractWorkflowEvent $workflowEvent)
 * @method static WorkflowRun dispatchRun(WorkflowRun $workflowRun, string $queue)
 * @method static WorkflowRun pauseRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun resumeRun(WorkflowRun $workflowRun)
 * @method static WorkflowRun cancelRun(WorkflowRun $workflowRun)
 * @method static WorkflowRunToken createInputParameter(WorkflowRun $workflowRun, string $key, mixed $value)
 * @method static WorkflowRunToken createOutputParameter(WorkflowRun $workflowRun, WorkflowActivity $workflowActivity, string $key, mixed $value)
 * @method static Workflow createWorkflow(string $name, WorkflowEvent|int $workflowEvent, WorkflowPriority|int $workflowPriority, int $retryInterval = 300)
 * @method static Workflow activateWorkflow(Workflow $workflow)
 * @method static Workflow deactivateWorkflow(Workflow $workflow)
 * @method static Workflow archiveWorkflow(Workflow $workflow)
 * @method static Workflow cloneWorkflow(Workflow $workflow, string $newWorkflowName)
 * @method static Workflow swapWorkflow(Workflow $workflowToDeactivate, Workflow $workflowToActivate)
 */
class Workflowable extends Facade
{
    /**
     * Identifies the default manager for the facade class
     */
    protected static function getFacadeAccessor(): string
    {
        return WorkflowableManager::class;
    }
}
