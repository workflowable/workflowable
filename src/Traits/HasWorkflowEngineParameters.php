<?php

namespace Workflowable\WorkflowEngine\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Workflowable\WorkflowEngine\Models\WorkflowEngineParameter;

trait HasWorkflowEngineParameters
{
    public function parameters(): MorphMany
    {
        return $this->morphMany(WorkflowEngineParameter::class, 'parameterizable');
    }
}
