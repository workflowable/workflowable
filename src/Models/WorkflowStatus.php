<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowStatus
 *
 * @property int $id
 * @property string $friendly_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowStatus extends Model
{
    use HasFactory;

    const DRAFT = 1;

    const ACTIVE = 2;

    const INACTIVE = 3;

    const ARCHIVED = 4;
}
