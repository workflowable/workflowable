<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

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
