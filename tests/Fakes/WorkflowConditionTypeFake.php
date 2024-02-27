<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Fields\Text;
use Workflowable\Form\Form;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowConditionTypeFake extends AbstractWorkflowConditionType
{
    public function handle(WorkflowProcess $workflowProcess, WorkflowCondition $workflowCondition): bool
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
