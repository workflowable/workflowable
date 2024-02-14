<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowConditionType
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowCondition> $workflowConditions
 * @property-read int|null $workflow_conditions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowEvent> $workflowEvents
 * @property-read int|null $workflow_events_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowConditionTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowConditionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_name',
    ];

    public function workflowEvents(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowEvent::class);
    }

    public function workflowConditions(): HasMany
    {
        return $this->hasMany(WorkflowCondition::class, 'workflow_condition_type_id');
    }
}
