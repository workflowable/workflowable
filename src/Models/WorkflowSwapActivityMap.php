<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workflowable\Workflowable\Concerns\HasFactory;

class WorkflowSwapActivityMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_workflow_activity_id',
        'to_workflow_activity_id',
        'workflow_swap_id',
    ];

    public function fromWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'from_workflow_activity_id');
    }

    public function toWorkflowActivity(): BelongsTo
    {
        return $this->belongsTo(WorkflowActivity::class, 'to_workflow_activity_id');
    }

    public function workflowSwap(): BelongsTo
    {
        return $this->belongsTo(WorkflowSwap::class, 'workflow_swap_id');
    }
}
