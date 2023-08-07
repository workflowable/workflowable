<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowProcessStatus
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowProcess> $workflowProcess
 * @property-read int|null $workflow_process_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowProcessStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowProcessStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowProcessStatus extends Model
{
    use HasFactory;

    /**
     * Indicates that we have created the process, but it is not ready to be picked up
     */
    const CREATED = 1;

    /**
     * Indicates that it is ready to be process
     */
    const PENDING = 2;

    /**
     * Indicates that we have dispatched the process to the queue
     */
    const DISPATCHED = 3;

    /**
     * We are actively attempting to process the process
     */
    const RUNNING = 4;

    /**
     * We've paused work on the process
     */
    const PAUSED = 5;

    /**
     * There was an error along the way
     */
    const FAILED = 6;

    /**
     * We've concluded all work for the process
     */
    const COMPLETED = 7;

    /**
     * The workflow process was cancelled
     */
    const CANCELLED = 8;

    public function workflowProcess(): HasMany
    {
        return $this->hasMany(WorkflowProcess::class, 'workflow_process_status_id');
    }
}
