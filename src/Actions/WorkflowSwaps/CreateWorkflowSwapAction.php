<?php

namespace Workflowable\Workflowable\Actions\WorkflowSwaps;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapCreated;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Models\WorkflowSwapActivityMap;

class CreateWorkflowSwapAction extends AbstractAction
{
    public function handle(Workflow $fromWorkflow, Workflow $toWorkflow): WorkflowSwap
    {
        if ($fromWorkflow->workflow_event_id !== $toWorkflow->workflow_event_id) {
            throw WorkflowSwapException::cannotPerformSwapBetweenWorkflowsOfDifferentEvents();
        }

        /** @var WorkflowSwap $workflowSwap */
        $workflowSwap = WorkflowSwap::query()->create([
            'from_workflow_id' => $fromWorkflow->id,
            'to_workflow_id' => $toWorkflow->id,
            'workflow_swap_status_id' => WorkflowSwapStatusEnum::Draft,
        ]);

        WorkflowSwapActivityMap::query()->insertUsing(
            ['from_workflow_activity_id', 'to_workflow_activity_id', 'workflow_swap_id'],
            WorkflowActivity::query()
                ->where('workflow_id', $fromWorkflow->id)
                ->selectRaw('id as from_workflow_activity_id')
                ->selectRaw('NULL as to_workflow_activity_id')
                ->selectRaw('? as workflow_swap_id', [$workflowSwap->id])
        );

        WorkflowSwapCreated::dispatch($workflowSwap);

        return $workflowSwap;
    }
}
