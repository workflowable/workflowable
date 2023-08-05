<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Traits\ValidatesWorkflowParameters;

class WorkflowConditionTypeEventConstrainedFake implements WorkflowConditionTypeContract
{
    use ValidatesWorkflowParameters;

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

    public function getRequiredWorkflowEventTokenKeys(): array
    {
        return [];
    }
}
