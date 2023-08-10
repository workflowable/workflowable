<?php

namespace Workflowable\Workflowable\Contracts;

interface WorkflowEventContract
{
    public function hasValidTokens(): bool;

    public function getTokens(): array;

    /**
     * A rule set that can be used to validate the data passed to the workflow event.  This should be formatted in
     * accordance with the Laravel validator
     *
     * @return array<string, mixed>
     */
    public function getRules(): array;

    /**
     * Identifies the queue the WorkflowProcessRunnerJob will be dispatched on for all workflow processes created by this event.
     */
    public function getQueue(): string;
}
