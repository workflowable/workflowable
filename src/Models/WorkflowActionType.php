<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowActionType
 *
 * @property int $id
 * @property string $friendly_name
 * @property string $alias
 * @property int|null $workflow_event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflow\Models\WorkflowEvent|null $workflowEvent
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowActionTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActionType whereWorkflowEventId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowActionType extends Model
{
    use HasFactory;

    protected $fillable = ['friendly_name', 'alias', 'workflow_event_id'];

    public function workflowEvent(): BelongsTo
    {
        return $this->belongsTo(WorkflowEvent::class, 'workflow_event_id');
    }
}
