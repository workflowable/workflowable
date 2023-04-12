<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowConditionType
 *
 * @property int $id
 * @property string $friendly_name
 * @property string $alias
 * @property int|null $workflow_event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflow\Models\WorkflowEvent|null $workflowEvent
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowConditionTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionType whereWorkflowEventId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowConditionType extends Model
{
    use HasFactory;

    protected $fillable = ['friendly_name', 'alias', 'workflow_event_id'];

    public function workflowEvent(): BelongsTo
    {
        return $this->belongsTo(WorkflowEvent::class, 'workflow_event_id');
    }
}
