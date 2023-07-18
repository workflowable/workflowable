<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowRun
 *
 * @property int $id
 * @property int $workflow_step_id
 * @property int $workflow_run_id
 * @property string $key,
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WorkflowRun|null $workflowRun
 * @property-read WorkflowStep|null $workflowStep
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowRunFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereWorkflowStepTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereWorkflowRunId($value)
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

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class, 'workflow_run_id');
    }

    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }
}
