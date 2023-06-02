<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowStep
 *
 * @property int $id
 * @property int $workflow_step_type_id
 * @property int $workflow_id
 * @property string $name
 * @property string|null $description
 * @property array|null $parameters
 * @property string $ux_uuid This is used to identify the condition in the UI.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflow\Models\WorkflowTransition> $nextWorkflowTransitions
 * @property-read int|null $next_workflow_transitions_count
 * @property-read \Workflowable\Workflow\Models\Workflow $workflow
 * @property-read \Workflowable\Workflow\Models\WorkflowStepType $workflowStepType
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowStepFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereWorkflowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereWorkflowStepTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStep whereUxUuid($value)
 *
 * @mixin \Eloquent
 */
class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id', 'workflow_step_type_id', 'name', 'description', 'parameters', 'ux_uuid'
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function workflowStepType(): BelongsTo
    {
        return $this->belongsTo(WorkflowStepType::class, 'workflow_step_type_id');
    }

    public function nextWorkflowTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'from_workflow_step_id');
    }
}
