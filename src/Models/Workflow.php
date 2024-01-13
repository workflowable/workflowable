<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Concerns\HasFactory;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;

/**
 * Workflowable\Workflowable\Models\Workflow
 *
 * @property int $id
 * @property string $name
 * @property int $workflow_event_id
 * @property WorkflowStatusEnum $workflow_status_id
 * @property int $retry_interval
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $workflow_priority_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowActivity> $workflowActivities
 * @property-read int|null $workflow_activities_count
 * @property-read \Workflowable\Workflowable\Models\WorkflowEvent $workflowEvent
 * @property-read \Workflowable\Workflowable\Models\WorkflowPriority $workflowPriority
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowProcess> $workflowProcesses
 * @property-read int|null $workflow_processes_count
 * @property-read \Workflowable\Workflowable\Models\WorkflowStatus $workflowStatus
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowTransition> $workflowTransitions
 * @property-read int|null $workflow_transitions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow active()
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow forEvent(\Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent|string|int $event)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow query()
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereRetryInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereWorkflowEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereWorkflowPriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Workflow whereWorkflowStatusId($value)
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
        'workflow_priority_id',
        'retry_interval',
    ];

    protected $casts = [
        'workflow_status_id' => WorkflowStatusEnum::class,
        'retry_interval' => 'integer',
    ];

    public function workflowEvent(): BelongsTo
    {
        return $this->belongsTo(WorkflowEvent::class, 'workflow_event_id');
    }

    public function workflowTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'workflow_id');
    }

    public function workflowActivities(): HasMany
    {
        return $this->hasMany(WorkflowActivity::class, 'workflow_id');
    }

    public function workflowStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowStatus::class, 'workflow_status_id');
    }

    public function workflowPriority(): BelongsTo
    {
        return $this->belongsTo(WorkflowPriority::class, 'workflow_priority_id');
    }

    public function workflowProcesses(): HasMany
    {
        return $this->hasMany(WorkflowProcess::class, 'workflow_id');
    }

    public function scopeActive($query)
    {
        return $query->where('workflow_status_id', WorkflowStatusEnum::ACTIVE->value);
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
