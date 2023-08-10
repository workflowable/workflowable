<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Abstracts\AbstractWorkflowActivityType;
use Workflowable\Workflowable\Contracts\ShouldRequireInputTokens;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowActivityTypeEventConstrainedFake extends AbstractWorkflowActivityType implements ShouldRestrictToWorkflowEvents, ShouldRequireInputTokens
{
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

    public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity): bool
    {
        return true;
    }

    public function getRequiredWorkflowEventTokenKeys(): array
    {
        return [];
    }
}
