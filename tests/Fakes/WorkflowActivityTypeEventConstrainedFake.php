<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Traits\ValidatesInputParameters;

class WorkflowActivityTypeEventConstrainedFake implements WorkflowActivityTypeContract
{
    use ValidatesInputParameters;

    public function getName(): string
    {
        return 'Workflow Activity Fake';
    }

    public function getAlias(): string
    {
        return 'workflow_activity_fake';
    }

    public function getRules(): array
    {
        return [
            'test' => 'required',
        ];
    }

    public function getWorkflowEventAliases(): array
    {
        return [
            (new WorkflowEventFake())->getAlias(),
        ];
    }

    public function handle(WorkflowRun $workflowRun, WorkflowActivity $workflowActivity): bool
    {
        return true;
    }

    public function getRequiredWorkflowEventParameterKeys(): array
    {
        return [];
    }
}
