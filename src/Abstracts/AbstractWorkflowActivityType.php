<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Concerns\GeneratesNameAndAliases;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;

abstract class AbstractWorkflowActivityType implements WorkflowActivityTypeContract
{
    use GeneratesNameAndAliases;
}
