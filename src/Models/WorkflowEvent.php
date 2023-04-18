<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property string $friendly_name
 * @property string $alias
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
        return $this->hasMany(WorkflowStepType::class, 'workflow_event_id');
    }
}
