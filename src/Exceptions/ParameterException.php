<?php

namespace Workflowable\Workflowable\Exceptions;

class ParameterException extends \Exception
{
    public static function unsupportedParameterType(string $type): ParameterException
    {
        return new ParameterException('The parameter type "'.$type.'" is not supported.');
    }
}
