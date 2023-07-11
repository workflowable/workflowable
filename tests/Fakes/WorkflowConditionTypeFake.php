<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowRun;

class WorkflowConditionTypeFake extends AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    public function getName(): string
    {
        return 'Workflow Condition Fake';
    }

    public function getAlias(): string
    {
        return 'workflow_condition_fake';
    }

    public function getRules(): array
    {
        return [
            'test' => 'required',
        ];
    }

    public function handle(WorkflowRun $workflowRun, WorkflowCondition $workflowCondition): bool
    {
        return true;
    }

    public function getWorkflowEventAliases(): array
    {
        return [];
    }
}
