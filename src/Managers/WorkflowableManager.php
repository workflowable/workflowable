<?php

namespace Workflowable\Workflowable\Managers;

use Workflowable\Workflowable\Traits\InteractsWithWorkflowRuns;
use Workflowable\Workflowable\Traits\InteractsWithWorkflows;

class WorkflowableManager
{
    use InteractsWithWorkflowRuns;
    use InteractsWithWorkflows;

    public function makeParameter(): ParameterManager
    {
        return new ParameterManager();
    }
}
