<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Traits\HasFactory;
use Workflowable\Workflowable\Traits\HasWorkflowableParameters;

/**
 * Workflowable\Workflow\Models\WorkflowRun
 *
 * @property int $id
 * @property int $workflow_id
 * @property int $workflow_run_status_id
 * @property int|null $last_workflow_step_id
 * @property array $parameters
 * @property Carbon|null $first_run_at
 * @property Carbon|null $last_run_at
 * @property Carbon $next_run_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $completed_at
 * @property-read WorkflowStep|null $lastWorkflowStep
 * @property-read Workflow $workflow
 * @property-read WorkflowRunStatus $workflowRunStatus
 * @property-read Collection|WorkflowableParameter[] $workflowRunParameters
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowRunFactory factory($count = null, $state = [])
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
    use HasWorkflowableParameters;

    protected array $dates = [
        'first_run_at',
        'last_run_at',
        'next_run_at',
        'completed_at',
    ];

    protected $fillable = [
        'workflow_id',
        'workflow_run_status_id',
        'last_workflow_step_id',
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

    public function lastWorkflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'last_workflow_step_id');
    }

    public function workflowRunStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowRunStatus::class, 'workflow_run_status_id');
    }
}
