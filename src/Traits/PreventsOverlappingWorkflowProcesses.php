<?php

namespace Workflowable\Workflowable\Traits;

/**
 * Sometimes you may want to prevent a workflow from running if there is already a workflow process in progress.
 * This trait will prevent a workflow from running if there is already a workflow process in progress for the
 * same workflow event class.  Alternatively, you can also provide a custom key to use for the lock by
 * overriding the `getWorkflowProcessLockKey` method.  This can be useful if you want to prevent multiple
 * workflows of different workflow event classes from running at the same time.
 */
trait PreventsOverlappingWorkflowProcesses
{
    /**
     * Defaults to the workflow even alias
     */
    public function getWorkflowProcessLockKey(): string
    {
        return $this->getAlias();
    }

    abstract public function getAlias(): string;
}