<?php

namespace Workflowable\WorkflowEngine\Contracts;

interface WorkflowEventContract
{
    public function hasValidParameters(): bool;

    public function getParameters(): array;

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
}
