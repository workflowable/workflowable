<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

class WorkflowTransition extends Model
{
    use HasFactory;

    public function fromWorkflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'from_workflow_step_id');
    }

    public function toWorkflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'to_workflow_step_id');
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function workflowConditions(): HasMany
    {
        return $this->hasMany(WorkflowCondition::class, 'workflow_transition_id');
    }
}
