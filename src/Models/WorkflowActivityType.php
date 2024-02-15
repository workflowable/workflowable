<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowActivityType
 *
 * @property int $id
 * @property string $name
 * @property string $class_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowActivity> $workflowActivities
 * @property-read int|null $workflow_activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowEvent> $workflowEvents
 * @property-read int|null $workflow_events_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityType query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowActivityType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'class_name'];

    public function workflowEvents(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowEvent::class);
    }

    public function workflowActivities(): HasMany
    {
        return $this->hasMany(WorkflowActivity::class, 'workflow_activity_type_id');
    }
}
