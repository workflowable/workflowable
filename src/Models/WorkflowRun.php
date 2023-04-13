<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property int $workflow_id
 * @property int $workflow_run_status_id
 * @property int|null $last_workflow_step_id
 * @property string|null $first_run_at
 * @property string|null $last_run_at
 * @property string $next_run_at
 * @property array $parameters
 * @property string $created_at
 * @property string $updated_at
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
