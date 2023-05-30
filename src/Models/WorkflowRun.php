<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowRun
 *
 * @property int $id
 * @property int $workflow_id
 * @property int $workflow_run_status_id
 * @property int|null $last_workflow_step_id
 * @property array $parameters
 * @property string|null $first_run_at
 * @property string|null $last_run_at
 * @property string $next_run_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflow\Models\WorkflowStep|null $lastWorkflowStep
 * @property-read \Workflowable\Workflow\Models\Workflow $workflow
 * @property-read \Workflowable\Workflow\Models\WorkflowRunStatus $workflowRunStatus
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowRunFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereFirstRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereLastRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereLastWorkflowStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereNextRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereWorkflowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereWorkflowRunStatusId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowRun extends Model
{
    use HasFactory;

    protected $casts = [
        'parameters' => 'json',
    ];

    protected $dates = [
        'first_run_at',
        'last_run_at',
        'next_run_at',
    ];

    protected $fillable = [
        'workflow_id',
        'workflow_run_status_id',
        'last_workflow_step_id',
        'first_run_at',
        'last_run_at',
        'next_run_at',
        'parameters',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function lastWorkflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'last_workflow_step_id');
    }

    public function workflowRunStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowRunStatus::class, 'workflow_run_status_id');
    }
}
