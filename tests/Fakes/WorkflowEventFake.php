<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Fields\Text\Text;
use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Contracts\ShouldPreventOverlappingWorkflowProcesses;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;

class WorkflowEventFake extends AbstractWorkflowEvent implements ShouldPreventOverlappingWorkflowProcesses, WorkflowEventContract
{
    public function makeForm(): FormManager
    {
        return FormManager::make([
            Text::make('Test', 'test')
                ->rules('required|string|min:4'),
        ]);
    }

    public function getWorkflowProcessLockKey(): string
    {
        return 'workflow_event_fake';
    }
}
