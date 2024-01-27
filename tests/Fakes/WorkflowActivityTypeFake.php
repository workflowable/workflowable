<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Facades\Form;
use Workflowable\Form\Fields\Selection\Select;
use Workflowable\Form\Fields\Text\Text;
use Workflowable\Form\FormManager;
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

    public function makeForm(): FormManager
    {
        return Form::make([
            Text::make('Test', 'test')
                ->rules(['required']),
            Select::make('Select', 'select')
                ->options([
                    'stuff' => 'Stuff',
                ]),
        ]);
    }
}
