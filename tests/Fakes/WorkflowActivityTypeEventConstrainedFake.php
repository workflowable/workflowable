<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Fields\Text\Number;
use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowActivityType;
use Workflowable\Workflowable\Contracts\ShouldRequireInputTokens;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowActivityTypeEventConstrainedFake extends AbstractWorkflowActivityType implements ShouldRequireInputTokens, ShouldRestrictToWorkflowEvents
{
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

    public function makeForm(): FormManager
    {
        return FormManager::make([
            Number::make('Test', 'test')
                ->min(1)
                ->max(10)
                ->step(1)
                ->rules('required'),
        ]);
    }

    public function getRequiredWorkflowEventTokenKeys(): array
    {
        return [];
    }
}
