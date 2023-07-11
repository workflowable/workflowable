<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowTransition
 *
 * @property int $id
 * @property string $name
 * @property int $workflow_id
 * @property ?int $from_workflow_step_id
 * @property int $to_workflow_step_id
 * @property int $ordinal This is used to determine the order the transitions are evaluated.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $ux_uuid This is used to identify the transition in the UI.
 * @property-read \Workflowable\Workflowable\Models\WorkflowStep $fromWorkflowStep
 * @property-read \Workflowable\Workflowable\Models\WorkflowStep $toWorkflowStep
 * @property-read \Workflowable\Workflowable\Models\Workflow $workflow
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowCondition> $workflowConditions
 * @property-read int|null $workflow_conditions_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowTransitionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereFromWorkflowStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereOrdinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereToWorkflowStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereWorkflowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereUxUuid($value)
 *
 * @mixin \Eloquent
 */
class WorkflowTransition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'from_workflow_step_id',
        'to_workflow_step_id',
        'workflow_id',
        'ordinal',
        'ux_uuid',
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
