<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Abstracts\AbstractWorkflowStepType;
use Workflowable\Workflowable\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowStep;

class WorkflowStepTypeEventConstrainedFake extends AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    public function getName(): string
    {
        return 'Workflow Step Fake';
    }

    public function getAlias(): string
    {
        return 'workflow_step_fake';
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

    public function handle(WorkflowRun $workflowRun, WorkflowStep $workflowStep): bool
    {
        return true;
    }
}
