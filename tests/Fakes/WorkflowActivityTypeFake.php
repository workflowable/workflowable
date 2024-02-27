<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Fields\Text;
use Workflowable\Form\Form;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowActivityTypeFake extends AbstractWorkflowActivityType
{
    public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity): bool
    {
        return true;
    }

    public function makeForm(): Form
    {
        return Form::make([
            Text::make('Test', 'test')
                ->rules(['required']),
        ]);
    }
}
