<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowSwap
 *
 * @property int $id
 * @property int $from_workflow_id
 * @property int $to_workflow_id
 * @property int $workflow_swap_status_id
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
 * @mixin \Eloquent
 */
class WorkflowSwap extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_workflow_id',
        'to_workflow_id',
        'workflow_swap_status_id',
    ];

    public function fromWorkflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'from_workflow_id');
    }

    public function toWorkflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'to_workflow_id');
    }

    public function workflowSwapStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowSwapStatus::class, 'workflow_swap_status_id');
    }

    public function workflowSwapActivityMaps(): HasMany
    {
        return $this->hasMany(WorkflowSwapActivityMap::class, 'workflow_swap_id');
    }
}
