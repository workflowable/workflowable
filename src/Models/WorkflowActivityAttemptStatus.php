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
 * @property-read \Workflowable\Workflowable\Models\WorkflowProcess $workflowProcess
 * @property-read \Workflowable\Workflowable\Models\WorkflowActivity $workflowActivity
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowActivityAttemptFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowActivity whereName($value)
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
