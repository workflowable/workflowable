<?php

namespace Workflowable\Workflow\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property string $friendly_name
 * @property string $alias
 * @property Carbon $create_at
 * @property Carbon $updated_at
 * @property Collection|WorkflowConditionType[] $workflowConditionTypes
 * @property Collection|WorkflowStepType[] $workflowStepTypes
 */
class WorkflowEvent extends Model
{
    use HasFactory;

    public function workflowConditionTypes(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowConditionType::class);
    }

    public function workflowStepTypes(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowStepType::class);
    }
}
