<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property string $friendly_name
 * @property string $alias
 * @property int $workflow_event_id
 * @property WorkflowEvent $workflowEvent
 */
class WorkflowConditionType extends Model
{
    use HasFactory;

    protected $fillable = ['friendly_name', 'alias', 'workflow_event_id'];

    public function workflowEvent(): BelongsTo
    {
        return $this->belongsTo(WorkflowEvent::class, 'workflow_event_id');
    }
}
