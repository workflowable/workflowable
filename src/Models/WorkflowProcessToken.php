<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowProcessToken
 *
 * @property int $id
 * @property int $workflow_process_id
 * @property int|null $workflow_activity_id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity|null $workflowActivity
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess $workflowProcess
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowProcessTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken whereWorkflowActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessToken whereWorkflowProcessId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowProcessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_process_id',
        'workflow_activity_id',
        'key',
        'value',
    ];

    public function workflowProcess(): BelongsTo
    {
        return $this->belongsTo(WorkflowProcess::class, 'workflow_process_id');
    }

    public function workflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'workflow_activity_id');
    }
}
