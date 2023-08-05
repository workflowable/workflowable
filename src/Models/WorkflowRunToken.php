<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowRunToken
 *
 * @property int $id
 * @property int $workflow_run_id
 * @property int|null $workflow_activity_id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WorkflowRun $workflowRun
 * @property-read WorkflowActivity|null $workflowActivity
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowRunTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken whereWorkflowRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken whereWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunToken whereType($value)
 *
 * @mixin \Eloquent
 */
class WorkflowRunToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_run_id',
        'workflow_activity_id',
        'key',
        'value',
        'type',
    ];

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class, 'workflow_run_id');
    }

    public function workflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'workflow_activity_id');
    }
}
