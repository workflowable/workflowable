<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowSignalStatus
 *
 * @property int $id
 * @property string $friendly_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowSignalStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalStatus whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalStatus whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowSignalStatus extends Model
{
    use HasFactory;
}
