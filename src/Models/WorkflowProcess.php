<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Concerns\HasFactory;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;

/**
 * Workflowable\Workflowable\Models\WorkflowProcess
 *
 * @property int $id
 * @property int $workflow_id
 * @property WorkflowProcessStatusEnum $workflow_process_status_id
 * @property int|null $last_workflow_activity_id
 * @property Carbon|null $first_run_at
 * @property Carbon|null $last_run_at
 * @property Carbon $next_run_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity|null $lastWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\Workflow $workflow
 * @property-read Collection<int, \Workflowable\Workflowable\Models\WorkflowProcessToken> $workflowProcessTokens
 * @property-read int|null $workflow_process_tokens_count
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcessStatus $workflowProcessStatus
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowProcessFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereFirstRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereLastRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereLastWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereNextRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereWorkflowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcess whereWorkflowProcessStatusId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowProcess extends Model
{
    use HasFactory;

    protected array $dates = [
        'first_run_at',
        'last_run_at',
        'next_run_at',
        'completed_at',
    ];

    protected $fillable = [
        'workflow_id',
        'workflow_process_status_id',
        'last_workflow_activity_id',
        'first_run_at',
        'last_run_at',
        'next_run_at',
        'completed_at',
    ];

    protected $casts = [
        'first_run_at' => 'date',
        'last_run_at' => 'date',
        'next_run_at' => 'date',
        'completed_at' => 'date',
        'workflow_process_status_id' => WorkflowProcessStatusEnum::class,
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function lastWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'last_workflow_activity_id');
    }

    public function workflowProcessStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowProcessStatus::class, 'workflow_process_status_id');
    }

    public function workflowProcessTokens(): HasMany
    {
        return $this->hasMany(WorkflowProcessToken::class, 'workflow_process_id');
    }
}
