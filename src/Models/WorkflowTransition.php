<?php

namespace Workflowable\Workflow\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property string $friendly_name
 * @property int $workflow_id
 * @property int $from_workflow_step_id
 * @property int $to_workflow_step_id
 * @property int $ordinal
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @param  Workflow  $workflow
 * @param  WorkflowStep  $fromWorkflowStep
 * @param  WorkflowStep  $toWorkflowStep
 * @param  WorkflowCondition[]  $workflowConditions
 */
class WorkflowTransition extends Model
{
    use HasFactory;

    protected $fillable = [
        'friendly_name',
        'from_workflow_step_id',
        'to_workflow_step_id',
        'workflow_id',
        'ordinal',
    ];

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
