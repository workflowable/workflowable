<?php

namespace Workflowable\Workflowable\Exceptions;

class WorkflowProcessException extends \Exception
{
    public static function workflowProcessNotEligibleForDispatch(): self
    {
        return new self('Workflow process is not eligible for dispatch at this time.');
    }

    public static function workflowProcessIsCurrentlyBeingProcessed(): self
    {
        return new self('This workflow process is currently being processed.');
    }
}
