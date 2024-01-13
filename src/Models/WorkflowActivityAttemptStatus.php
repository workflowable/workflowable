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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowActivityAttempt> $workflowActivityAttempts
 * @property-read int|null $workflow_activity_attempts_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityAttemptStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttemptStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttemptStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttemptStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttemptStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttemptStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttemptStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivityAttemptStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowActivityAttemptStatus extends Model
{
    use HasFactory;

    public function workflowActivityAttempts(): HasMany
    {
        return $this->hasMany(WorkflowActivityAttempt::class, 'workflow_activity_attempt_status_id');
    }
}
