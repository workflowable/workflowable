<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowConditionParameter
 *
 * @property int $id
 * @property int|null $workflow_condition_id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowCondition|null $workflowCondition
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowConditionParameterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConditionParameter whereWorkflowConditionId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowConditionParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_condition_id',
        'key',
        'value',
    ];

    public function workflowCondition(): BelongsTo
    {
        return $this->belongsTo(WorkflowCondition::class, 'workflow_condition_id');
    }
}
