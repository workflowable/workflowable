<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowSignal
 *
 * @property int $id
 * @property int $workflow_run_id
 * @property string $sourceable_type
 * @property int $sourceable_id
 * @property int $workflow_run_status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowSignalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal whereSourceableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal whereSourceableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal whereWorkflowRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignal whereWorkflowRunStatusId($value)
 *
 * @mixin \Eloquent
 */
class WorkflowSignal extends Model
{
    use HasFactory;
}
