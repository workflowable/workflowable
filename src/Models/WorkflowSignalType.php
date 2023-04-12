<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Workflowable\Workflow\Traits\HasFactory;

/**
 * Workflowable\Workflow\Models\WorkflowSignalType
 *
 * @property int $id
 * @property string $friendly_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Workflowable\Workflow\Database\Factories\WorkflowSignalTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalType query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalType whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowSignalType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WorkflowSignalType extends Model
{
    use HasFactory;
}
