<?php

namespace Workflowable\WorkflowEngine\Concerns;

/**
 * Sometimes you may want to prevent a workflow from running if there is already a workflow run in progress.
 * This trait will prevent a workflow from running if there is already a workflow run in progress for the
 * same workflow event class.  Alternatively, you can also provide a custom key to use for the lock by
 * overriding the `getWorkflowRunLockKey` method.  This can be useful if you want to prevent multiple
 * workflows of different workflow event classes from running at the same time.
 */
trait PreventOverlappingWorkflowRuns
{
    /**
     * Defaults to the workflow even alias
     *
     * @return string
     */
    public function getWorkflowRunLockKey(): string
    {
        return $this->getAlias();
    }

    abstract function getAlias(): string;
}
