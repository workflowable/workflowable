<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowActivityCompletion
 *
 * @property int $id
 * @property int $workflow_process_id
 * @property int $workflow_activity_id
 * @property Carbon $started_at
 * @property Carbon $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess $workflowProcess
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity $workflowActivity
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityCompletionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereWorkflowRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereWorkflowActivityId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowActivityCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_process_id', 'workflow_activity_id', 'started_at', 'completed_at',
    ];

    protected $dates = [
        'started_at', 'completed_at',
    ];

    protected $casts = [
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