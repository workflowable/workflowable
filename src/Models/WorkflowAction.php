<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowAction
 *
 * @property int $id
 * @property int $workflow_action_type_id
 * @property int $workflow_id
 * @property string $friendly_name
 * @property string|null $description
 * @property mixed|null $parameters
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowTransition> $nextWorkflowTransitions
 * @property-read int|null $next_workflow_transitions_count
 * @property-read \Workflowable\Workflow\Models\Workflow $workflow
 * @property-read \Workflowable\Workflow\Models\WorkflowActionType $workflowActionType
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowActionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction whereWorkflowActionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowAction whereWorkflowId($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowTransition> $nextWorkflowTransitions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowTransition> $nextWorkflowTransitions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowTransition> $nextWorkflowTransitions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowTransition> $nextWorkflowTransitions
 *
 * @mixin \Eloquent
 */
class WorkflowAction extends Model
{
    use HasFactory;

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function workflowActionType(): BelongsTo
    {
        return $this->belongsTo(WorkflowActionType::class, 'workflow_action_type_id');
    }

    public function nextWorkflowTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'from_workflow_action_id');
    }
}
