<?php

namespace Workflowable\Workflow\Exceptions;

class WorkflowException extends \Exception
{
    public static function workflowAlreadyActive(): static
    {
        return new static('The workflow is already active.');
    }

    public static function workflowAlreadyInactive(): static
    {
        return new static('The workflow is already inactive.');
    }

    public static function workflowCannotBeArchivedFromActiveState(): static
    {
        return new static('The workflow cannot be archived from an active state.');
    }

    public static function cannotArchiveWorkflowWithActiveRuns(): static
    {
        return new static('The workflow cannot be archived while it has active runs.');
    }
}
