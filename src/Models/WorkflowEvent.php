<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowEvent
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowConditionType> $workflowConditionTypes
 * @property-read int|null $workflow_condition_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowActivityType> $workflowActivityTypes
 * @property-read int|null $workflow_activity_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\Workflow> $workflows
 * @property-read int|null $workflows_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alias',
    ];

    public function workflowConditionTypes(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowConditionType::class);
    }

    public function workflowActivityTypes(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowActivityType::class);
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class, 'workflow_event_id');
    }
}
