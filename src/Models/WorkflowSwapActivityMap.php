<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowSwapActivityMap
 *
 * @property int $id
 * @property int $workflow_swap_id
 * @property int $from_workflow_activity_id
 * @property int|null $to_workflow_activity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity $fromWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity|null $toWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowSwap $workflowSwap
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowSwapActivityMapFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap whereFromWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap whereToWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapActivityMap whereWorkflowSwapId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowSwapActivityMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_workflow_activity_id',
        'to_workflow_activity_id',
        'workflow_swap_id',
    ];

    public function fromWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'from_workflow_activity_id');
    }

    public function toWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'to_workflow_activity_id');
    }

    public function workflowSwap(): BelongsTo
    {
        return $this->belongsTo(WorkflowSwap::class, 'workflow_swap_id');
    }
}
