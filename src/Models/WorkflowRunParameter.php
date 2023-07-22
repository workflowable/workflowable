<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Casts\ParameterCast;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowRunParameter
 *
 * @property int $id
 * @property int $workflow_run_id
 * @property int|null $workflow_step_id
 * @property string $key
 * @property string $value
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowRun $workflowRun
 * @property-read \Workflowable\Workflowable\Models\WorkflowStep|null $workflowStep
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowRunParameterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter whereWorkflowRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter whereWorkflowStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunParameter whereType($value)
 *
 * @mixin \Eloquent
 */
class WorkflowRunParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_run_id',
        'workflow_step_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => ParameterCast::class,
    ];

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class, 'workflow_run_id');
    }

    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }
}
