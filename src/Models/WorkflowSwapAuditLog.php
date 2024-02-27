<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowSwapAuditLog
 *
 * @property int $id
 * @property int $workflow_swap_id
 * @property int $from_workflow_process_id
 * @property int $from_workflow_activity_id
 * @property int $to_workflow_process_id
 * @property int|null $to_workflow_activity_id When no activity is provided to transition to, we will start over from the beginning
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity $fromWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess $fromWorkflowProcess
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity|null $toWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess $toWorkflowProcess
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess $workflowSwap
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowSwapAuditLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereFromWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereFromWorkflowProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereToWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereToWorkflowProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereWorkflowSwapId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowSwapAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_swap_id',
        'from_workflow_process_id',
        'from_workflow_activity_id',
        'to_workflow_process_id',
        'to_workflow_activity_id',
    ];

    public function workflowSwap(): BelongsTo
    {
        return $this->belongsTo(WorkflowProcess::class, 'workflow_process_id');
    }

    public function fromWorkflowProcess(): BelongsTo
    {
        return $this->belongsTo(WorkflowProcess::class, 'from_workflow_process_id');
    }

    public function fromWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'from_workflow_activity_id');
    }

    public function toWorkflowProcess(): BelongsTo
    {
        return $this->belongsTo(WorkflowProcess::class, 'to_workflow_process_id');
    }

    public function toWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'to_workflow_activity_id');
    }
}
