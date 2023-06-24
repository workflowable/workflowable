<?php

namespace Workflowable\WorkflowEngine\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Workflowable\WorkflowEngine\Traits\HasFactory;

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
