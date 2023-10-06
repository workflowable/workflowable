<?php

namespace Workflowable\Workflowable\Abstracts;

use Workflowable\Workflowable\Concerns\GeneratesNameAndAliases;
use Workflowable\Workflowable\Concerns\InteractsWithWorkflowProcesses;
use Workflowable\Workflowable\Concerns\ValidatesWorkflowParameters;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;

abstract class AbstractWorkflowActivityType implements WorkflowActivityTypeContract
{
    use GeneratesNameAndAliases;
    use InteractsWithWorkflowProcesses;
    use ValidatesWorkflowParameters;
}
