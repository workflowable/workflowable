<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property int $workflow_transition_id
 * @property int $workflow_condition_type_id
 * @property array $parameters
 * @property WorkflowTransition $workflowTransition
 * @property WorkflowConditionType $workflowConditionType
 */
class WorkflowCondition extends Model
{
    use HasFactory;

    public function workflowTransition(): BelongsTo
    {
        return $this->belongsTo(WorkflowTransition::class, 'workflow_transition_id');
    }

    public function workflowConditionType(): BelongsTo
    {
        return $this->belongsTo(WorkflowConditionType::class, 'workflow_condition_type_id');
    }
}
