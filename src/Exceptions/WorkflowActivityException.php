<?php

namespace Workflowable\Workflowable\Exceptions;

class WorkflowActivityException extends \Exception
{
    public static function workflowActivityTypeNotRegistered(): self
    {
        return new self('The workflow activity type is not registered.');
    }

    public static function workflowActivityTypeParametersInvalid(): self
    {
        return new self('The workflow activity type parameters are invalid.');
    }

    public static function workflowActivityDoesNotBelongToWorkflow(): self
    {
        return new self('The workflow activity does not belong to the given workflow.');
    }
}
