<?php

namespace Workflowable\Workflow\Contracts;

use Workflowable\Workflow\Models\WorkflowAction;
use Workflowable\Workflow\Models\WorkflowRun;

/**
 * Interface WorkflowActionContract
 */
interface WorkflowActionContract
{
    /**
     * A friendly name that can be used to identify the workflow action.  This should be unique to the workflow action
     * and may change over time.
     */
    public function getFriendlyName(): string;

    /**
     * An alias that can be used to identify the workflow action.  This should be unique to the workflow action
     * and should not change over time.
     */
    public function getAlias(): string;

    /**
     * A rule set that can be used to validate the data passed to the workflow action.  This should be formatted in
     * accordance with the Laravel validator
     *
     * @return array<string, string>
     */
    public function getRules(): array;

    /**
     * Identifies that the workflow action can only be executed when a specific workflow event is triggered.
     *
     * If null is returned, the workflow action can be used across any workflow event.
     */
    public function getWorkflowEventAlias(): ?string;

    /**
     * The business logic that will be used to execute the workflow action.
     */
    public function handle(WorkflowRun $workflowRun, WorkflowAction $workflowAction): bool;
}
