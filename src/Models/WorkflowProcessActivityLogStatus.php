<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowActivityAttemptStatus
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowProcessActivityLog> $workflowProcessActivityLogs
 * @property-read int|null $workflow_process_activity_logs_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityAttemptStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessActivityLogStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessActivityLogStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessActivityLogStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessActivityLogStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessActivityLogStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessActivityLogStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessActivityLogStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowProcessActivityLogStatus extends Model
{
    use HasFactory;

    public function workflowProcessActivityLogs(): HasMany
    {
        return $this->hasMany(WorkflowProcessActivityLog::class, 'workflow_process_activity_log_status_id');
    }
}
