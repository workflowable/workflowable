<?php

namespace Workflowable\WorkflowEngine;

use Workflowable\WorkflowEngine\Traits\HandlesWorkflowRuns;
use Workflowable\WorkflowEngine\Traits\HandlesWorkflows;

class WorkflowEngine
{
    use HandlesWorkflowRuns;
    use HandlesWorkflows;
}
