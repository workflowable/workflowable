<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Fields\Text;
use Workflowable\Form\Form;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Contracts\ShouldPreventOverlappingWorkflowProcesses;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;

class WorkflowEventFake extends AbstractWorkflowEvent implements ShouldPreventOverlappingWorkflowProcesses, WorkflowEventContract
{
    public function makeForm(): Form
    {
        return Form::make([
            Text::make('Test', 'test')
                ->rules('required|string|min:4'),
        ]);
    }

    public function getWorkflowProcessLockKey(): string
    {
        return 'workflow_event_fake';
    }
}
