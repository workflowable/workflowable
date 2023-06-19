<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\Workflow
 *
 * @property int $id
 * @property string $name
 * @property int $workflow_event_id
 * @property int $workflow_status_id
 * @property string $ux_uuid
 * @property int $retry_interval
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowStep> $workflowSteps
 * @property-read int|null $workflow_steps_count
 * @property-read \Workflowable\Workflow\Models\WorkflowEvent $workflowEvent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowRun> $workflowRuns
 * @property-read int|null $workflow_runs_count
 * @property-read \Workflowable\Workflow\Models\WorkflowStatus $workflowStatus
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowTransition> $workflowTransitions
 * @property-read int|null $workflow_transitions_count
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow query()
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereWorkflowEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereWorkflowStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow active()
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow forEvent(AbstractWorkflowEvent|string|int $value)
 *
 * @mixin \Eloquent
 */
class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_event_id',
        'workflow_status_id',
        'name',
        'retry_interval',
    ];

    public function workflowEvent(): BelongsTo
    {
        return $this->belongsTo(WorkflowEvent::class, 'workflow_event_id');
    }

    public function workflowTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'workflow_id');
    }

    public function workflowSteps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class, 'workflow_id');
    }

    public function workflowStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowStatus::class, 'workflow_status_id');
    }

    public function workflowRuns(): HasMany
    {
        return $this->hasMany(WorkflowRun::class, 'workflow_id');
    }

    public function scopeActive($query)
    {
        return $query->where('workflow_status_id', WorkflowStatus::ACTIVE);
    }

    public function scopeForEvent($query, AbstractWorkflowEvent|string|int $event)
    {
        return $query->whereHas('workflowEvent', function ($query) use ($event) {
            match (true) {
                is_int($event) => $query->where('workflow_events.id', $event),
                is_string($event) => $query->where('workflow_events.alias', $event),
                $event instanceof AbstractWorkflowEvent => $query->where('alias', $event->getAlias()),
            };
        });
    }
}
