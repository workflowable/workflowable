<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Forms\Fields\Text\Text;
use Workflowable\Forms\Form;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowActivityTypeFake extends AbstractWorkflowActivityType
{
    public function getRules(): array
    {
        return [
            'test' => 'required',
        ];
    }

    public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity): bool
    {
        return true;
    }

    public function makeForm(Form $form): Form
    {
        return $form->make([
            Text::make('Test', 'test')
                ->setValue('Test'),
        ]);
    }
}
