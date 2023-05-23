<?php

namespace Workflowable\Workflow\Tests\Fakes;

use Workflowable\Workflow\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Models\WorkflowCondition;
use Workflowable\Workflow\Models\WorkflowRun;

class WorkflowConditionTypeEventConstrainedFake extends AbstractWorkflowConditionType implements WorkflowConditionTypeContract
{
    public function getFriendlyName(): string
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

    public function getWorkflowEventAlias(): ?string
    {
        return (new WorkflowEventFake())->getAlias();
    }
}
