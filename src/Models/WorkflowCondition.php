<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflowable\Traits\HasFactory;
use Workflowable\Workflowable\Traits\HasWorkflowConfigurationParameters;

/**
 * Workflowable\Workflow\Models\WorkflowCondition
 *
 * @property int $id
 * @property int $workflow_transition_id
 * @property int $workflow_condition_type_id
 * @property int $ordinal This is used to determine the order the conditions are evaluated.
 * @property string $ux_uuid This is used to identify the condition in the UI.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflowable\Models\WorkflowConditionType $workflowConditionType
 * @property-read \Workflowable\Workflowable\Models\WorkflowTransition $workflowTransition
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowConditionParameter> $workflowConfigurationParameters
 * @property-read int|null $workflow_configuration_parameters_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowConditionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition whereOrdinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition whereWorkflowConditionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition whereWorkflowTransitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowCondition whereUxUuid($value)
 *
 * @mixin \Eloquent
 */
class WorkflowCondition extends Model
{
    use HasFactory;
    use HasWorkflowConfigurationParameters;

    protected $fillable = [
        'workflow_transition_id',
        'workflow_condition_type_id',
        'ordinal',
    ];

    public function workflowTransition(): BelongsTo
    {
        return $this->belongsTo(WorkflowTransition::class, 'workflow_transition_id');
    }

    public function workflowConditionType(): BelongsTo
    {
        return $this->belongsTo(WorkflowConditionType::class, 'workflow_condition_type_id');
    }
}
