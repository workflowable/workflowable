<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflowable\Concerns\HasFactory;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;

/**
 * Workflowable\Workflowable\Models\WorkflowSwap
 *
 * @property int $id
 * @property int $from_workflow_id
 * @property int $to_workflow_id
 * @property WorkflowSwapStatusEnum $workflow_swap_status_id
 * @property string|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\Workflow $fromWorkflow
 * @property-read \Workflowable\Workflowable\Models\Workflow $toWorkflow
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowSwapActivityMap> $workflowSwapActivityMaps
 * @property-read int|null $workflow_swap_activity_maps_count
 * @property-read \Workflowable\Workflowable\Models\WorkflowSwapStatus $workflowSwapStatus
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowSwapFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereFromWorkflowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereToWorkflowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereWorkflowSwapStatusId($value)
 *
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity $fromWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess|null $fromWorkflowProcess
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity $toWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess|null $toWorkflowProcess
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess $workflowSwap
 * @property int $workflow_swap_id
 * @property int $from_workflow_process_id
 * @property int $from_workflow_activity_id
 * @property int $to_workflow_process_id
 * @property int|null $to_workflow_activity_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereFromWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereFromWorkflowProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereToWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapAuditLog whereToWorkflowProcessId($value)
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
        'from_workflow_process_activity_id',
        'to_workflow_process_id',
        'to_workflow_process_activity_id',
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
        return $this->belongsTo(WorkflowProcess::class, 'from_workflow_process_id');
    }

    public function toWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'from_workflow_activity_id');
    }
}
