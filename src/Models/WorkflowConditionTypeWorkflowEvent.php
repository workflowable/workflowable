<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowConditionTypeWorkflowEvent
 *
 * @property int $id
 * @property int $workflow_event_id
 * @property int $workflow_condition_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowConditionType $workflowConditionType
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivityType $workflowEvent
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowConditionTypeWorkflowEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionTypeWorkflowEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionTypeWorkflowEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionTypeWorkflowEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionTypeWorkflowEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionTypeWorkflowEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionTypeWorkflowEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionTypeWorkflowEvent whereWorkflowConditionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionTypeWorkflowEvent whereWorkflowEventId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowConditionTypeWorkflowEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_event_id',
        'workflow_condition_type_id',
    ];

    protected $table = 'workflow_condition_type_workflow_event';

    public function workflowConditionType(): BelongsTo
    {
        return $this->belongsTo(WorkflowConditionType::class, 'workflow_condition_type_id');
    }

    public function workflowEvent(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivityType::class, 'workflow_event_id');
    }
}
