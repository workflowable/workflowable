<?php

namespace Workflowable\Workflowable\Managers;

use Workflowable\Workflowable\Concerns\InteractsWithWorkflowProcesses;
use Workflowable\Workflowable\Concerns\InteractsWithWorkflows;

class WorkflowableManager
{
    use InteractsWithWorkflowProcesses;
    use InteractsWithWorkflows;
}
