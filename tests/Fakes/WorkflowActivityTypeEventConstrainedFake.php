<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Fields\Number;
use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowActivityType;
use Workflowable\Workflowable\Contracts\ShouldRequireInputTokens;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowActivityTypeEventConstrainedFake extends AbstractWorkflowActivityType implements ShouldRequireInputTokens, ShouldRestrictToWorkflowEvents, WorkflowActivityTypeContract
{
    public function getRestrictedWorkflowEventClasses(): array
    {
        return [
            WorkflowEventFake::class,
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
