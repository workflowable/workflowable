<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowTransition
 *
 * @property int $id
 * @property string $name
 * @property int $workflow_id
 * @property int|null $from_workflow_activity_id
 * @property int $to_workflow_activity_id
 * @property int $ordinal This is used to determine the order the transitions are evaluated.
 * @property string|null $ux_uuid This is used to identify the transition in the UI.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity|null $fromWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity $toWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\Workflow $workflow
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowCondition> $workflowConditions
 * @property-read int|null $workflow_conditions_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowTransitionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereFromWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereOrdinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereToWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereUxUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereWorkflowId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowTransition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'from_workflow_activity_id',
        'to_workflow_activity_id',
        'workflow_id',
        'ordinal',
        'ux_uuid',
    ];

    public function fromWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'from_workflow_activity_id');
    }

    public function toWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'to_workflow_activity_id');
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
