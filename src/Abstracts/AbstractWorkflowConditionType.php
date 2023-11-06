<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Concerns\GeneratesNameAndAliases;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;

abstract class AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    use GeneratesNameAndAliases;
}
