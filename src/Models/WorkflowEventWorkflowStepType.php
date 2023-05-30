<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowEventWorkflowStepType
 *
 * @property int $id
 * @property int $workflow_event_id
 * @property int $workflow_step_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflow\Models\WorkflowStepType $workflowEvent
 * @property-read \Workflowable\Workflow\Models\WorkflowStepType $workflowStepType
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowEventWorkflowStepTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEventWorkflowStepType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEventWorkflowStepType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEventWorkflowStepType query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEventWorkflowStepType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEventWorkflowStepType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEventWorkflowStepType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEventWorkflowStepType whereWorkflowEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEventWorkflowStepType whereWorkflowStepTypeId($value)
 *
 * @mixin \Eloquent
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
