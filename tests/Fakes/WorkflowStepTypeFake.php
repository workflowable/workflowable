<?php

namespace Workflowable\Workflow\Tests\Fakes;

use Workflowable\Workflow\Abstracts\AbstractWorkflowStepType;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowStep;

class WorkflowStepTypeFake extends AbstractWorkflowStepType implements WorkflowStepTypeContract
{
    public function getFriendlyName(): string
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

    public function getWorkflowEventAlias(): ?string
    {
        return null;
    }

    public function handle(WorkflowRun $workflowRun, WorkflowStep $workflowStep): bool
    {
        return true;
    }
}
