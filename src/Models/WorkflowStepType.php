<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Class WorkflowStepType
 *
 * @property int $id
 * @property string $friendly_name
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class WorkflowStepType extends Model
{
    use HasFactory;

    protected $fillable = ['friendly_name', 'alias'];

    public function workflowEvents(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowEvent::class);
    }
}
