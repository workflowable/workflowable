<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Form;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflowable\Contracts\ShouldRequireInputTokens;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowConditionTypeEventConstrainedFake extends AbstractWorkflowConditionType implements ShouldRequireInputTokens, ShouldRestrictToWorkflowEvents
{
    public function getRules(): array
    {
        return [
            'test' => 'required',
        ];
    }

    public function handle(WorkflowProcess $workflowProcess, WorkflowCondition $workflowCondition): bool
    {
        return true;
    }

    public function getWorkflowEventAliases(): array
    {
        return [
            (new WorkflowEventFake())->getAlias(),
        ];
    }

    public function makeForm(): Form
    {
        return Form::make();
    }

    public function getRequiredWorkflowEventTokenKeys(): array
    {
        return [];
    }
}
