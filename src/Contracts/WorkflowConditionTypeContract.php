<?php

namespace Workflowable\Workflow\Contracts;

use Workflowable\Workflow\Models\WorkflowCondition;
use Workflowable\Workflow\Models\WorkflowRun;

interface WorkflowConditionTypeContract
{
    /**
     * A friendly name that can be used to identify the workflow condition.  This should be unique to the workflow
     * condition and may change over time.
     */
    public function getFriendlyName(): string;

    /**
     * An alias that can be used to identify the workflow condition.  This should be unique to the workflow condition
     * and should not change over time.
     */
    public function getAlias(): string;

    /**
     * A rule set that can be used to validate the data passed to the workflow condition.  This should be formatted in
     * accordance with the Laravel validator
     *
     * @return array<string, string>
     */
    public function getRules(): array;

    /**
     * Identifies that the workflow condition can only be executed when a specific workflow event is triggered.
     *
     * If null is returned, the workflow condition can be used across any workflow event.
     */
    public function getWorkflowEventAlias(): ?string;

    /**
     * The business logic that will be used to evaluate the workflow condition.
     */
    public function handle(WorkflowRun $workflowRun, WorkflowCondition $workflowCondition): bool;
}
