<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowTransition
 *
 * @property int $id
 * @property string $friendly_name
 * @property int $workflow_id
 * @property int $from_workflow_action_id
 * @property int $to_workflow_action_id
 * @property int $ordinal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Workflowable\Workflow\Models\WorkflowAction $fromWorkflowAction
 * @property-read \Workflowable\Workflow\Models\WorkflowAction $toWorkflowAction
 * @property-read \Workflowable\Workflow\Models\Workflow $workflow
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowTransitionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereFromWorkflowActionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereOrdinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereToWorkflowActionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowTransition whereWorkflowId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowTransition extends Model
{
    use HasFactory;

    public function fromWorkflowAction(): BelongsTo
    {
        return $this->belongsTo(WorkflowAction::class, 'from_workflow_action_id');
    }

    public function toWorkflowAction(): BelongsTo
    {
        return $this->belongsTo(WorkflowAction::class, 'to_workflow_action_id');
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }
}
