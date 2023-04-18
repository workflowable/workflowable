<?php

namespace Workflowable\Workflow\Tests\Fakes;

use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Traits\ValidatesWorkflowParameters;

class WorkflowStepTypeFake implements WorkflowStepTypeContract
{
    use ValidatesWorkflowParameters;

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
