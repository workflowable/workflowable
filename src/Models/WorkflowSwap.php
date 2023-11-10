<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\Workflowable\Concerns\HasFactory;

class WorkflowSwap extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_workflow_id',
        'to_workflow_id',
        'workflow_swap_status_id',
    ];

    public function fromWorkflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'from_workflow_id');
    }

    public function toWorkflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'to_workflow_id');
    }

    public function workflowSwapStatus(): BelongsTo
    {
        return $this->belongsTo(WorkflowSwapStatus::class, 'workflow_swap_status_id');
    }

    public function workflowSwapActivityMaps(): HasMany
    {
        return $this->hasMany(WorkflowSwapActivityMap::class, 'workflow_swap_id');
    }
}
