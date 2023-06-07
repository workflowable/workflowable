<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowRun
 *
 * @property int $id
 * @property int $workflow_run_id
 * @property string $name,
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WorkflowRun|null $workflowRun
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowRunFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRun whereName($value)
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
        'name',
        'value',
    ];

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class, 'workflow_run_id', 'id');
    }
}
