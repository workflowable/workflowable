<?php

namespace Workflowable\Workflow\Contracts;

interface WorkflowEventContract
{
    /**
     * @param array $parameters
     * @return bool
     */
    public function hasValidParameters(array $parameters): bool;

    /**
     * An alias that can be used to identify the workflow event.  This should be unique to the workflow event and
     * should not change over time.
     */
    public function getAlias(): string;

    /**
     * A friendly name that can be used to identify the workflow event.  This should be unique to the workflow event
     * and may change over time.
     */
    public function getFriendlyName(): string;

    /**
     * A rule set that can be used to validate the data passed to the workflow event.  This should be formatted in
     * accordance with the Laravel validator
     *
     * @return array<string, string>
     */
    public function getRules(): array;
}
