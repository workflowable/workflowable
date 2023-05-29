<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $alias
 */
class WorkflowEvent extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'alias',
    ];

    public function workflowConditionTypes(): HasMany
    {
        return $this->hasMany(WorkflowConditionType::class, 'workflow_event_id');
    }

    public function workflowActionTypes(): HasMany
    {
        return $this->hasMany(WorkflowStepType::class, 'workflow_event_id');
    }
}
