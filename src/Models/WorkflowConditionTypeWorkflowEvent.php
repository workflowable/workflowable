<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property WorkflowConditionType $workflowConditionType
 * @property WorkflowEvent $workflowEvent
 * @property int $workflow_event_id
 * @property int $workflow_condition_type_id
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
        return $this->belongsTo(WorkflowStepType::class, 'workflow_event_id');
    }
}
