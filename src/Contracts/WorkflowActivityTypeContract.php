<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Workflowable\Builders\FormBuilder;
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
     * The business logic that will be used to execute the workflow activity.
     */
    public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity): bool;

    /**
     * The form that will be used to collect the parameters for the workflow activity.
     *
     * @param FormBuilder $form
     * @return FormBuilder
     */
    public function makeForm(FormBuilder $form): FormBuilder;
}
