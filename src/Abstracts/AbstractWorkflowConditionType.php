<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Concerns\GeneratesNameAndAliases;
use Workflowable\Workflowable\Concerns\ValidatesWorkflowParameters;

abstract class AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    use ValidatesWorkflowParameters;
    use GeneratesNameAndAliases;
}
