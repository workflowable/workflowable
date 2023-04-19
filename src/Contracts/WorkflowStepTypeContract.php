<?php

namespace Workflowable\Workflow\Contracts;

use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowStep;

/**
 * Interface WorkflowStepTypeContract
 */
interface WorkflowStepTypeContract
{
    /**
     * A friendly name that can be used to identify the workflow step.  This should be unique to the workflow step
     * and may change over time.
     */
    public function getFriendlyName(): string;

    /**
     * An alias that can be used to identify the workflow step.  This should be unique to the workflow step
     * and should not change over time.
     */
    public function getAlias(): string;

    /**
     * Determines if the parameters passed to the workflow step are valid.
     *
     * @return bool
     */
    public function hasValidParameters(): bool;

    /**
     * A rule set that can be used to validate the data passed to the workflow step.  This should be formatted in
     * accordance with the Laravel validator
     *
     * @return array<string, string>
     */
    public function getRules(): array;

    /**
     * Identifies that the workflow step can only be executed when a specific workflow event is triggered.
     *
     * If null is returned, the workflow step can be used across any workflow event.
     */
    public function getWorkflowEventAlias(): ?string;

    /**
     * The business logic that will be used to execute the workflow step.
     */
    public function handle(WorkflowRun $workflowRun, WorkflowStep $workflowStep): bool;
}
