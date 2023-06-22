<?php

namespace Workflowable\WorkflowEngine\Models;

use Illuminate\Database\Eloquent\Model;
use Workflowable\WorkflowEngine\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowRunLog
 *
 * @property int $id
 * @property string $loggable_type
 * @property int $loggable_id
 * @property string $level
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Workflowable\WorkflowEngine\Database\Factories\WorkflowRunLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog whereLoggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog whereLoggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowRunLog extends Model
{
    use HasFactory;
}
