<?php

namespace Workflowable\Workflowable\Exceptions;

class ParameterException extends \Exception
{
    public static function unsupportedParameterType(string $type): static
    {
        return new static('The parameter type "'.$type.'" is not supported.');
    }
}
