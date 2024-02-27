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
    public function handle(WorkflowProcess $workflowProcess, WorkflowCondition $workflowCondition): bool
    {
        return true;
    }

    public function getRestrictedWorkflowEventClasses(): array
    {
        return [
            WorkflowEventFake::class,
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
