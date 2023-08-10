<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowActivity
 *
 * @property int $id
 * @property int $workflow_activity_type_id
 * @property int $workflow_id
 * @property string $name
 * @property string|null $description
 * @property string|null $ux_uuid This is used to identify the activity in the UI.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowTransition> $nextWorkflowTransitions
 * @property-read int|null $next_workflow_transitions_count
 * @property-read \Workflowable\Workflowable\Models\Workflow $workflow
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowActivityParameter> $workflowActivityParameters
 * @property-read int|null $workflow_activity_parameters_count
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivityType $workflowActivityType
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereUxUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereWorkflowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereWorkflowActivityTypeId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id', 'workflow_activity_type_id', 'name', 'description', 'ux_uuid',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function workflowActivityType(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivityType::class, 'workflow_activity_type_id');
    }

    public function workflowActivityParameters(): HasMany
    {
        return $this->hasMany(WorkflowActivityParameter::class, 'workflow_activity_id');
    }

    public function nextWorkflowTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'from_workflow_activity_id');
    }
}
