<?php

namespace Workflowable\Workflow\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property Carbon $created_at
 */
class WorkflowConditionType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'alias', 'workflow_event_id'];

    public function workflowEvents(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowEvent::class);
    }
}
