<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowSwapStatus
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Workflowable\Workflowable\Models\WorkflowSwap> $workflowSwaps
 * @property-read int|null $workflow_swaps_count
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowSwapStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSwapStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowSwapStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function workflowSwaps(): HasMany
    {
        return $this->hasMany(WorkflowSwap::class, 'workflow_swap_id');
    }
}
