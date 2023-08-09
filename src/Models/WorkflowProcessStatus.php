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

    public function workflowProcess(): HasMany
    {
        return $this->hasMany(WorkflowProcess::class, 'workflow_process_status_id');
    }
}
