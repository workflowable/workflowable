<?php

namespace Workflowable\Workflow\Exceptions;

class WorkflowStepException extends \Exception
{
    public static function workflowStepTypeNotRegistered(): self
    {
        return new self('The workflow step type is not registered.');
    }

    public static function workflowStepTypeParametersInvalid(): self
    {
        return new self('The workflow step type parameters are invalid.');
    }

    public static function workflowStepDoesNotBelongToWorkflow(): self
    {
        return new self('The workflow step does not belong to the given workflow.');
    }
}
