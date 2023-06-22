<?php

namespace Workflowable\WorkflowEngine\Exceptions;

class WorkflowEventException extends \Exception
{
    public static function invalidWorkflowEventParameters(): self
    {
        return new self('The workflow event parameters are invalid.');
    }

    public static function workflowEventNotRegistered(): self
    {
        return new self('The workflow event is not registered.');
    }
}
