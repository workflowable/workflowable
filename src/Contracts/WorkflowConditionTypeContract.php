<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowProcess;

interface WorkflowConditionTypeContract
{
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
     * The business logic that will be used to evaluate the workflow condition.
     */
    public function handle(WorkflowProcess $workflowProcess, WorkflowCondition $workflowCondition): bool;

    /**
     * The form that will be used to collect the parameters for the workflow condition.
     */
    public function makeForm(): FormManager;
}
