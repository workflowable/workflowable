<?php

namespace Workflowable\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Workflowable\Workflow\Traits\HasFactory;

class WorkflowStatus extends Model
{
    use HasFactory;

    const DRAFT = 1;

    const ACTIVE = 2;

    const INACTIVE = 3;

    const ARCHIVED = 4;
}
