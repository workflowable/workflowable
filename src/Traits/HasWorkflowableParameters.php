<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Workflowable\Workflowable\Models\WorkflowableParameter;

trait HasWorkflowableParameters
{
    public function parameters(): MorphMany
    {
        return $this->morphMany(WorkflowableParameter::class, 'parameterizable');
    }
}
