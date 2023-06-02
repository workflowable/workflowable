<?php

namespace Workflowable\Workflow\Actions\WorkflowConditions;

use Workflowable\Workflow\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflow\Models\WorkflowCondition;

class CreateWorkflowConditionAction
{
    public function handle(WorkflowConditionData $workflowConditionData): WorkflowCondition
    {

        return new WorkflowCondition();
    }
}
