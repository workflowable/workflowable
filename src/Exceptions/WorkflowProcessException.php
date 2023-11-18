<?php

namespace Workflowable\Workflowable\Exceptions;

class WorkflowProcessException extends \Exception
{
    public static function workflowProcessNotEligibleForDispatch(): self
    {
        return new self('Workflow process is not eligible for dispatch at this time.');
    }
}
