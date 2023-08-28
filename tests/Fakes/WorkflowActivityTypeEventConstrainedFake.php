<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Abstracts\AbstractWorkflowActivityType;
use Workflowable\Workflowable\Builders\FormBuilder;
use Workflowable\Workflowable\Contracts\ShouldRequireInputTokens;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Fields\Selection\Select;
use Workflowable\Workflowable\Fields\Text\Number;
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

    public function makeForm(FormBuilder $form): FormBuilder
    {
        return $form->make([
            Number::make('Test', 'test')
                ->min(1)
                ->max(10)
                ->step(1)
                ->rules('required'),
            Select::make('Options', 'options')
                ->options([
                    'option1' => 'Option 1',
                    'option2' => 'Option 2',
                ])
                ->rules([])
                ->helpText('Select an option'),
        ]);
    }

    public function getRequiredWorkflowEventTokenKeys(): array
    {
        return [];
    }
}
