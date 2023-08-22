<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflowable\Builders\FormBuilder;
use Workflowable\Workflowable\Contracts\ShouldRequireInputTokens;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowConditionTypeEventConstrainedFake extends AbstractWorkflowConditionType implements ShouldRestrictToWorkflowEvents, ShouldRequireInputTokens
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

    public function makeForm(FormBuilder $form): FormBuilder
    {
        return $form;
    }

    public function getRequiredWorkflowEventTokenKeys(): array
    {
        return [];
    }
}
