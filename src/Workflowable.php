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
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;

/**
 * @method static Collection triggerEvent(AbstractWorkflowEvent $workflowEvent)
 * @method static WorkflowProcess createWorkflowProcess(Workflow $workflow, AbstractWorkflowEvent $workflowEvent)
 * @method static WorkflowProcess dispatchProcess(WorkflowProcess $workflowProcess, string $queue)
 * @method static WorkflowProcess pauseProcess(WorkflowProcess $workflowProcess)
 * @method static WorkflowProcess resumeProcess(WorkflowProcess $workflowProcess)
 * @method static WorkflowProcess cancelProcess(WorkflowProcess $workflowProcess)
 * @method static WorkflowProcessToken createInputParameter(WorkflowProcess $workflowProcess, string $key, mixed $value)
 * @method static WorkflowProcessToken createOutputParameter(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity, string $key, mixed $value)
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
