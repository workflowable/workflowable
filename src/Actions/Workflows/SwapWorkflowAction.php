<?php

namespace Workflowable\Workflowable\Actions\Workflows;

use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Models\Workflow;

class SwapWorkflowAction extends AbstractAction
{
    public function handle(Workflow $workflowToDeactivate, Workflow $workflowToActivate): Workflow
    {
        DB::transaction(function () use ($workflowToActivate, $workflowToDeactivate) {
            DeactivateWorkflowAction::make()->handle($workflowToDeactivate);
            ActivateWorkflowAction::make()->handle($workflowToActivate);
        });

        return $workflowToActivate;
    }
}
