<?php

namespace Workflowable\WorkflowEngine\Tests\Fakes;

use Workflowable\WorkflowEngine\Abstracts\AbstractWorkflowStepType;
use Workflowable\WorkflowEngine\Contracts\WorkflowStepTypeContract;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowStep;

class WorkflowStepTypeFake extends AbstractWorkflowStepType implements WorkflowStepTypeContract
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
        return [];
    }

    public function handle(WorkflowRun $workflowRun, WorkflowStep $workflowStep): bool
    {
        return true;
    }
}
