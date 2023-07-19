<?php

namespace Workflowable\Workflowable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Workflowable\Workflowable\Traits\HasFactory;

/**
 * Workflowable\Workflowable\Models\WorkflowConfigurationParameter
 *
 * @property int $id
 * @property string $parameterizable_type
 * @property int $parameterizable_id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Eloquent $parameterizable
 *
 * @method static \Workflowable\Workflowable\Database\Factories\WorkflowConfigurationParameterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter whereParameterizableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter whereParameterizableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkflowConfigurationParameter whereValue($value)
 *
 * @mixin \Eloquent
 */
class WorkflowConfigurationParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'parameterizable_id',
        'parameterizable_type',
        'key',
        'value',
    ];

    public function parameterizable(): MorphTo
    {
        return $this->morphTo();
    }
}
