<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowActivity;

/**
 * Interface WorkflowActivityTypeContract
 */
interface WorkflowActivityTypeContract
{
    /**
     * A name that can be used to identify the workflow activity.  This should be unique to the workflow activity
     * and may change over time.
     */
    public function getName(): string;

    /**
     * An alias that can be used to identify the workflow activity.  This should be unique to the workflow activity
     * and should not change over time.
     */
    public function getAlias(): string;

    /**
     * Determines if the parameters passed to the workflow activity are valid.
     */
    public function hasValidParameters(): bool;

    /**
     * A rule set that can be used to validate the data passed to the workflow activity.  This should be formatted in
     * accordance with the Laravel validator
     *
     * @return array<string, mixed>
     */
    public function getRules(): array;

    /**
     * Identifies that the workflow activity can only be executed when a specific workflow event is triggered.
     *
     * If null is returned, the workflow activity can be used across any workflow event.
     */
    public function getWorkflowEventAliases(): array;

    /**
     * The business logic that will be used to execute the workflow activity.
     */
    public function handle(WorkflowRun $workflowRun, WorkflowActivity $workflowActivity): bool;
}
