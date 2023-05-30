<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowEvent
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowConditionType> $workflowConditionTypes
 * @property-read int|null $workflow_condition_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowStepType> $workflowStepTypes
 * @property-read int|null $workflow_step_types_count
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowEventFactory factory($count = null, $state = [])
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

    public function workflowStepTypes(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowStepType::class);
    }
}
