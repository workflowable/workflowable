<?php

namespace Workflowable\Workflowable\Contracts;

use Workflowable\Form\FormManager;

interface WorkflowEventContract
{
    public function hasValidTokens(): bool;

    public function getTokens(): array;

    /**
     * A name that can be used to identify the workflow event.  This should be unique to the workflow event
     * and may change over time.
     */
    public function getName(): string;

    /**
     * The form that will be used to collect the input tokens for a workflow event.
     */
    public function makeForm(): FormManager;

    /**
     * Identifies the queue the WorkflowProcessRunnerJob will be dispatched on for all workflow processes created by this event.
     */
    public function getQueue(): string;
}
