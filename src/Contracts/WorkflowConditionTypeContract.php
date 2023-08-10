<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowProcess;

interface WorkflowConditionTypeContract
{
    /**
     * Determines if the parameters passed to the workflow condition type are valid.
     */
    public function hasValidParameters(): bool;

    /**
     * A name that can be used to identify the workflow condition.  This should be unique to the workflow
     * condition and may change over time.
     */
    public function getName(): string;

    /**
     * An alias that can be used to identify the workflow condition.  This should be unique to the workflow condition
     * and should not change over time.
     */
    public function getAlias(): string;

    /**
     * A rule set that can be used to validate the data passed to the workflow condition.  This should be formatted in
     * accordance with the Laravel validator
     *
     * @return array<string, mixed>
     */
    public function getRules(): array;

    /**
     * The business logic that will be used to evaluate the workflow condition.
     */
    public function handle(WorkflowProcess $workflowProcess, WorkflowCondition $workflowCondition): bool;
}
