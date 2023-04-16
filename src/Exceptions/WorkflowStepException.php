<?php

namespace Workflowable\Workflow\Exceptions;

class WorkflowStepException extends \Exception
{
    public static function workflowStepTypeNotRegistered(string $alias): self
    {
        return new self("The workflow step type [{$alias}] is not registered.");
    }

    public static function workflowStepTypeParametersInvalid(string $alias): self
    {
        return new self("The workflow step type [{$alias}] parameters are invalid.");
    }
}
