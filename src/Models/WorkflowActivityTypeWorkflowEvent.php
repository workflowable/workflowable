<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowActivityTypeWorkflowEvent
 *
 * @property int $id
 * @property int $workflow_event_id
 * @property int $workflow_activity_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivityType $workflowEvent
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivityType $workflowActivityType
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityTypeWorkflowEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityTypeWorkflowEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityTypeWorkflowEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityTypeWorkflowEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityTypeWorkflowEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityTypeWorkflowEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityTypeWorkflowEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityTypeWorkflowEvent whereWorkflowEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityTypeWorkflowEvent whereWorkflowActivityTypeId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowActivityTypeWorkflowEvent extends Model
{
    use HasFactory;

    protected $table = 'workflow_activity_type_workflow_event';

    protected $fillable = [
        'workflow_event_id',
        'workflow_activity_type_id',
    ];

    public function workflowActivityType(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivityType::class, 'workflow_activity_type_id');
    }

    public function workflowEvent(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivityType::class, 'workflow_event_id');
    }
}
