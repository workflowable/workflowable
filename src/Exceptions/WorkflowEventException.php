<?php

namespace Workflowable\Workflow\Exceptions;

use Workflowable\Workflow\Contracts\WorkflowEventContract;

class WorkflowEventException extends \Exception
{
    public static function invalidWorkflowEventParameters(): self
    {
        return new self('The workflow event parameters are invalid.');
    }

    public static function workflowEventNotRegistered(WorkflowEventContract|string $workflowEvent): static
    {
        $alias = $workflowEvent instanceof WorkflowEventContract ? $workflowEvent->getAlias() : $workflowEvent;

        return new static("The workflow event [{$alias}] is not registered.");
    }
}
