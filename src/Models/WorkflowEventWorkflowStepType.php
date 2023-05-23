<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property WorkflowStepType $workflowStepType
 * @property WorkflowEvent $workflowEvent
 * @property int $workflow_event_id
 * @property int $workflow_step_type_id
 */
class WorkflowEventWorkflowStepType extends Model
{
    use HasFactory;

    protected $table = 'workflow_event_workflow_step_type';

    protected $fillable = [
        'workflow_event_id',
        'workflow_step_type_id',
    ];

    public function workflowStepType(): BelongsTo
    {
        return $this->belongsTo(WorkflowStepType::class, 'workflow_step_type_id');
    }

    public function workflowEvent(): BelongsTo
    {
        return $this->belongsTo(WorkflowStepType::class, 'workflow_event_id');
    }
}
