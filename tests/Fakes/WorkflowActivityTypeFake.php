<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Traits\ValidatesWorkflowParameters;

class WorkflowActivityTypeFake implements WorkflowActivityTypeContract
{
    use ValidatesWorkflowParameters;

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
        return [];
    }

    public function handle(WorkflowRun $workflowRun, WorkflowActivity $workflowActivity): bool
    {
        return true;
    }

    public function getRequiredWorkflowEventTokenKeys(): array
    {
        return [];
    }
}
