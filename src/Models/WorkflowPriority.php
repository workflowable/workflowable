<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowPriority
 *
 * @property int $id
 * @property string $name
 * @property int $priority
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\Workflow> $workflows
 * @property-read int|null $workflows_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowPriorityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowPriority whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
    ];

    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }
}
