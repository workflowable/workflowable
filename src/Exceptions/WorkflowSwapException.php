<?php

namespace Workflowable\Workflowable\Exceptions;

class WorkflowSwapException extends \Exception
{
    public static function cannotPerformSwapBetweenWorkflowsOfDifferentEvents(): self
    {
        return new static('Cannot perform workflow swaps between incompatible workflow events');
    }
}
