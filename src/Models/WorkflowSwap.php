<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;

/**
 * Workflowable\Workflowable\Models\WorkflowSwap
 *
 * @property int $id
 * @property int $from_workflow_id
 * @property int $to_workflow_id
 * @property WorkflowSwapStatusEnum $workflow_swap_status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\Workflow $fromWorkflow
 * @property-read \Workflowable\Workflowable\Models\Workflow $toWorkflow
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowSwapActivityMap> $workflowSwapActivityMaps
 * @property-read int|null $workflow_swap_activity_maps_count
 * @property-read \Workflowable\Workflowable\Models\WorkflowSwapStatus $workflowSwapStatus
 * @property \Illuminate\Support\Carbon|null $scheduled_at Used for scheduling a workflow swap for a date and time in the future
 * @property \Illuminate\Support\Carbon|null $dispatched_at Indicates when the system dispatched the job to process the swap
 * @property \Illuminate\Support\Carbon|null $started_at Indicates the time the system was actually able to begin working on the swap
 * @property \Illuminate\Support\Carbon|null $completed_at Indicates when we have completed a workflow swap
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
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereDispatchedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereStartedAt($value)
 *
 * @property int $should_transfer_output_tokens
 *
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwap whereShouldTransferOutputTokens($value)
 *
 * @mixin \Eloquent
 */
class WorkflowSwap extends Model
{
    use HasFactory;

    protected $dates = [
        'scheduled_at',
        'dispatched_at',
        'started_at',
        'completed_at',
    ];

    protected $fillable = [
        'from_workflow_id',
        'to_workflow_id',
        'workflow_swap_status_id',
        'should_transfer_output_tokens',
        'scheduled_at',
        'dispatched_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'workflow_swap_status_id' => WorkflowSwapStatusEnum::class,
        'scheduled_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
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
