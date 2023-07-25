<?php

namespace Workflowable\Workflowable\Exceptions;

class ParameterException extends \Exception
{
    public static function unableToRetrieveParameterForType(string $type): ParameterException
    {
        return new ParameterException('The parameter type "'.$type.'" is not supported.');
    }

    public static function unableToPrepareParameterForStorage(): ParameterException
    {
        return new ParameterException('Unable to prepare parameter for storage.');
    }

    public static function invalidModel(string $type = null): ParameterException
    {
        return new ParameterException('Unable to match parameter conversion to model for type "'.$type.'".');
    }
}
