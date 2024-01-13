<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Concerns\HasFactory;
use Workflowable\Workflowable\Enums\WorkflowActivityAttemptStatusEnum;

/**
 * Workflowable\Workflowable\Models\WorkflowActivityAttempt
 *
 * @property int $id
 * @property int $workflow_process_id The workflow run we completed the activity on
 * @property int $workflow_activity_id The activity that was completed
 * @property WorkflowActivityAttemptStatusEnum $workflow_activity_attempt_status_id The status of the attempt
 * @property Carbon $started_at
 * @property Carbon $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity $workflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess $workflowProcess
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityAttemptFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt whereWorkflowActivityAttemptStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt whereWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttempt whereWorkflowProcessId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowActivityAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_process_id',
        'workflow_activity_id',
        'workflow_activity_attempt_status_id',
        'started_at',
        'completed_at',
    ];

    protected $dates = [
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'workflow_activity_attempt_status_id' => WorkflowActivityAttemptStatusEnum::class,
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * The workflow run that we completed the activity on
     *
     * @returns BelongsTo
     */
    public function workflowProcess(): BelongsTo
    {
        return $this->belongsTo(WorkflowProcess::class, 'workflow_process_id');
    }

    /**
     * Identifies the activity that was completed
     *
     * @returns BelongsTo
     */
    public function workflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'workflow_activity_id');
    }
}
