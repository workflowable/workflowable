<?php

namespace Workflowable\Workflowable\Tests\Fakes;

use Workflowable\Form\Fields\Text\Text;
use Workflowable\Form\FormManager;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Concerns\PreventsOverlappingWorkflowProcesses;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;

class WorkflowEventFake extends AbstractWorkflowEvent implements WorkflowEventContract
{
    use PreventsOverlappingWorkflowProcesses;

    public function makeForm(): FormManager
    {
        return FormManager::make([
            Text::make('Test', 'test')
                ->rules('required|string|min:4')
        ]);
    }
}
