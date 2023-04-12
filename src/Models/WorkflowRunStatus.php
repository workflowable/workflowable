<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowRunStatus
 *
 * @property int $id
 * @property string $friendly_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowRunStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunStatus whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowRunStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowRunStatus extends Model
{
    use HasFactory;

    /**
     * Indicates that we have created the core run, but it is not ready to be picked up
     */
    const CREATED = 1;

    /**
     * Indicates that it is ready to be run
     */
    const PENDING = 2;

    /**
     * Indicates that we have dispatched the core run to the queue
     */
    const DISPATCHED = 3;

    /**
     * We are actively attempting to process the core run
     */
    const RUNNING = 4;

    /**
     * We've paused work on the run
     */
    const PAUSED = 5;

    /**
     * There was an error along the way
     */
    const FAILED = 6;

    /**
     * We've concluded all work for the core run
     */
    const COMPLETED = 7;

    /**
     * The workflow run was cancelled
     */
    const CANCELLED = 8;
}
