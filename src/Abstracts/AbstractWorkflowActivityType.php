<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Concerns\GeneratesHumanReadableNameForClass;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;

abstract class AbstractWorkflowActivityType implements WorkflowActivityTypeContract
{
    use GeneratesHumanReadableNameForClass;
}
