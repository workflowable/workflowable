<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowRun
 *
 * @property int $id
 * @property int $workflow_id
 * @property int $workflow_run_status_id
 * @property int|null $last_workflow_activity_id
 * @property Carbon|null $first_run_at
 * @property Carbon|null $last_run_at
 * @property Carbon $next_run_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity|null $lastWorkflowActivity
 * @property-read \Workflowable\Workflowable\Models\Workflow $workflow
 * @property-read Collection<int, \Workflowable\Workflowable\Models\WorkflowRunParameter> $workflowRunParameters
 * @property-read int|null $workflow_run_parameters_count
 * @property-read \Workflowable\Workflowable\Models\WorkflowRunStatus $workflowRunStatus
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowRunFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereFirstRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereLastRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereLastWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereNextRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereWorkflowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereWorkflowRunStatusId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowRun extends Model
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
        'workflow_run_status_id',
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
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function lastWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'last_workflow_activity_id');
    }

    public function workflowRunStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowRunStatus::class, 'workflow_run_status_id');
    }

    public function workflowRunParameters(): HasMany
    {
        return $this->hasMany(WorkflowRunParameter::class, 'workflow_run_id');
    }
}
