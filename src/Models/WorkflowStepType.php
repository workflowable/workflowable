<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowStepType
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowEvent> $workflowEvents
 * @property-read int|null $workflow_events_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowStepTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStepType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStepType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStepType query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStepType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStepType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStepType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStepType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStepType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowStepType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'alias'];

    public function workflowEvents(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowEvent::class);
    }
}
