<?php

namespace Workflowable\WorkflowEngine\Tests\Fakes;

use Workflowable\WorkflowEngine\Abstracts\AbstractWorkflowConditionType;
use Workflowable\WorkflowEngine\Contracts\WorkflowConditionTypeContract;
use Workflowable\WorkflowEngine\Models\WorkflowCondition;
use Workflowable\WorkflowEngine\Models\WorkflowRun;

class WorkflowConditionTypeEventConstrainedFake extends AbstractWorkflowConditionType implements WorkflowConditionTypeContract
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
        return [
            (new WorkflowEventFake())->getAlias(),
        ];
    }
}
