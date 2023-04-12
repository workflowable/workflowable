<?php

namespace Workflowable\Workflow\Actions\Workflows;

use Illuminate\Support\Collection;
use Workflowable\Workflow\Models\Workflow;

/**
 * Used to save the structure of a core including the transitions and actions that will be used.
 */
class SaveWorkflowStructureAction
{
    public function handle(Workflow $workflow, Collection $workflowTransitions, Collection $workflowActions): Workflow
    {
        $hasWorkflowRuns = $workflow->workflowRuns()
            ->exists();

        if ($hasWorkflowRuns) {
            throw new \Exception('Cannot update a core that has core runs');
        }

        $workflow->workflowTransitions()->delete();
        $workflow->workflowActions()->delete();

        $this->handleCreatingWorkflowActions($workflow, $workflowActions);
        $this->handleCreatingWorkflowTransitions($workflow, $workflowTransitions);

        return $workflow;
    }

    public function handleCreatingWorkflowActions(Workflow $workflow, Collection $workflowActions): void
    {
    }

    public function handleCreatingWorkflowTransitions(Workflow $workflow, Collection $workflowTransitions): void
    {
    }
}
