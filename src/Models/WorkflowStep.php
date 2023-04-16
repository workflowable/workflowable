<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * @property int $id
 * @property int $workflow_id
 * @property int $workflow_step_type_id
 * @property string $friendly_name
 * @property string $description
 * @property array $parameters
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Workflowable\Workflow\Models\Workflow $workflow
 * @property-read \Workflowable\Workflow\Models\WorkflowStepType $workflowStepType
 * @property-read \Illuminate\Database\Eloquent\Collection|\Workflowable\Workflow\Models\WorkflowTransition[] $nextWorkflowTransitions
 * @property-read int|null $next_workflow_transitions_count
 */
class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id', 'workflow_step_type_id', 'friendly_name', 'description', 'parameters',
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
