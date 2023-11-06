<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Forms\Form;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

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
     * The business logic that will be used to execute the workflow activity.
     */
    public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity): bool;

    /**
     * The form that will be used to collect the parameters for the workflow activity.
     */
    public function makeForm(): Form;
}
