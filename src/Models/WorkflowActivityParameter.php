<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowActivityParameter
 *
 * @property int $id
 * @property int|null $workflow_activity_id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity|null $workflowActivity
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityParameterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityParameter whereWorkflowActivityId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowActivityParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_activity_id',
        'key',
        'value',
    ];

    public function workflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'workflow_activity_id');
    }
}
