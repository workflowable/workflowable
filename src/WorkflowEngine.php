<?php

namespace Workflowable\WorkflowEngine;

use Workflowable\WorkflowEngine\Traits\InteractsWithWorkflowRuns;
use Workflowable\WorkflowEngine\Traits\InteractsWithWorkflows;

class WorkflowEngine
{
    use InteractsWithWorkflowRuns;
    use InteractsWithWorkflows;
}
