<?php

namespace Workflowable\Workflowable\Contracts;

interface WorkflowEventContract
{
    public function hasValidTokens(): bool;

    public function getTokens(): array;

    /**
     * An alias that can be used to identify the workflow event.  This should be unique to the workflow event and
     * should not change over time.
     */
    public function getAlias(): string;

    /**
     * A name that can be used to identify the workflow event.  This should be unique to the workflow event
     * and may change over time.
     */
    public function getName(): string;

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
