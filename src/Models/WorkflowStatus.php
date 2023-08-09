<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowStatus
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\Workflow> $workflows
 * @property-read int|null $workflows_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowStatus extends Model
{
    use HasFactory;

    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class, 'workflow_status_id');
    }
}
