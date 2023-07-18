<?php

namespace Workflowable\Workflowable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Workflowable\Workflowable\Models\WorkflowConfigurationParameter;

trait HasWorkflowConfigurationParameters
{
    public function workflowConfigurationParameters(): MorphMany
    {
        return $this->morphMany(WorkflowConfigurationParameter::class, 'parameterizable');
    }
}
