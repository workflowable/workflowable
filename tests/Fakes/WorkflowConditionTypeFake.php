<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflowable\Builders\FormBuilder;
use Workflowable\Workflowable\Fields\Text\Text;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowProcess;

class WorkflowConditionTypeFake extends AbstractWorkflowConditionType
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

    public function makeForm(FormBuilder $form): FormBuilder
    {
        return $form->text('Test', 'test', function (Text $text) {
            $text->setValue('Test');
        });
    }
}
