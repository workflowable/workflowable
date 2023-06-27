<?php

namespace Workflowable\WorkflowEngine\Exceptions;

class WorkflowException extends \Exception
{
    public static function workflowAlreadyActive(): self
    {
        return new self('The workflow is already active.');
    }

    public static function workflowAlreadyDeactivated(): self
    {
        return new self('The workflow is already deactivated.');
    }

    public static function workflowCannotBeArchivedFromActiveState(): self
    {
        return new self('The workflow cannot be archived from an active state.');
    }

    public static function cannotArchiveWorkflowWithActiveRuns(): self
    {
        return new self('The workflow cannot be archived while it has active runs.');
    }

    public static function cannotModifyWorkflowNotInDraftState(): self
    {
        return new self('The workflow cannot be modified while it is not in draft state.');
    }
}
