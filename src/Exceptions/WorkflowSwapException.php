<?php

namespace Workflowable\Workflowable\Exceptions;

class WorkflowSwapException extends \Exception
{
    public static function cannotPerformSwapBetweenWorkflowsOfDifferentEvents(): self
    {
        return new self('Cannot perform workflow swaps between incompatible workflow events');
    }

    public static function workflowSwapInProcess(): self
    {
        return new self('Workflow swap in process');
    }

    public static function workflowSwapNotEligibleForDispatch(): self
    {
        return new self('Workflow swap is not eligible for dispatch');
    }

    public static function missingWorkflowSwapActivityMap(): self
    {
        return new self('Cannot perform workflow swap due to missing activity map');
    }
}
