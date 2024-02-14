<?php

namespace Workflowable\Workflowable\Actions\WorkflowEvents;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Models\WorkflowEvent;

class RegisterWorkflowEventAction extends AbstractAction
{
    public function handle(WorkflowEventContract $workflowEventContract): WorkflowEvent
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::query()->create([
            'name' => $workflowEventContract->getName(),
            'class_name' => $workflowEventContract::class,
        ]);

        return $workflowEvent;
    }
}
