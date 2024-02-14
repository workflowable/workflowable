<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Concerns\GeneratesHumanReadableNameForClass;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;

abstract class AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    use GeneratesHumanReadableNameForClass;
}
