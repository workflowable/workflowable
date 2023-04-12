<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowEvent
 *
 * @property int $id
 * @property string $friendly_name
 * @property string $alias
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowActionType> $workflowActionTypes
 * @property-read int|null $workflow_action_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowConditionType> $workflowConditionTypes
 * @property-read int|null $workflow_condition_types_count
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowEventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowEvent whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowActionType> $workflowActionTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowConditionType> $workflowConditionTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowActionType> $workflowActionTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowConditionType> $workflowConditionTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowActionType> $workflowActionTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowConditionType> $workflowConditionTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowActionType> $workflowActionTypes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowConditionType> $workflowConditionTypes
 *
 * @mixin \Eloquent
 */
class WorkflowEvent extends Model
{
    use HasFactory;

    public function workflowConditionTypes(): HasMany
    {
        return $this->hasMany(WorkflowConditionType::class, 'workflow_event_id');
    }

    public function workflowActionTypes(): HasMany
    {
        return $this->hasMany(WorkflowActionType::class, 'workflow_event_id');
    }
}
