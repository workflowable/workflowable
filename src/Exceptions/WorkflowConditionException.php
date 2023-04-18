<?php

namespace Workflowable\Workflow\Exceptions;

class WorkflowConditionException extends \Exception
{
    public static function workflowConditionTypeNotRegistered(): self
    {
        return new self('The workflow condition type is not registered.');
    }

    public static function workflowConditionTypeParametersInvalid(string $alias): self
    {
        return new self("The workflow condition type [{$alias}] parameters are invalid.");
    }

    public static function workflowConditionTypeNotEligibleForEvent(string $alias): self
    {
        return new self("The workflow condition type [{$alias}] is not eligible for the event.");
    }
}
