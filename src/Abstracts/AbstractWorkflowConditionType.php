<?php

namespace Workflowable\Workflow\Abstracts;

use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Traits\ValidatesWorkflowParameters;

abstract class AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    use ValidatesWorkflowParameters;

    public function __construct(protected array $parameters = [])
    {

    }
}
