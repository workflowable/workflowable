<?php

namespace Workflowable\Workflowable\Exceptions;

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

    public static function cannotArchiveWorkflowWithActiveProcesses(): self
    {
        return new self('The workflow cannot be archived while it has active processes.');
    }

    public static function workflowNotEditable(): self
    {
        return new self('This workflow is no longer in an editable state.');
    }

    public static function cannotModifyEventForExistingWorkflow(): self
    {
        return new self('Changing the event for an existing workflow is not supported');
    }
}
