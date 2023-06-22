<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\WorkflowEngine\Actions\WorkflowTransitions\DeleteWorkflowTransitionAction;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;
use Workflowable\WorkflowEngine\Tests\TestCase;
use Workflowable\WorkflowEngine\Tests\Traits\HasWorkflowRunTests;

class DeleteWorkflowTransitionActionTest extends TestCase
{
    use HasWorkflowRunTests;

    public function test_that_we_can_delete_a_workflow_transition(): void
    {
        $this->workflow->update(['workflow_status_id' => WorkflowStatus::DRAFT]);
        $deleteTransitionAction = app(DeleteWorkflowTransitionAction::class);

        $deleteTransitionAction->handle($this->workflowTransition);
        $this->assertDatabaseMissing(WorkflowTransition::class, [
            'id' => $this->workflowTransition->id,
        ]);
    }

    public function test_that_we_can_cannot_delete_a_workflow_transition_from_a_active_workflow(): void
    {
        $deleteTransitionAction = app(DeleteWorkflowTransitionAction::class);

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotModifyWorkflowNotInDraftState()->getMessage());
        $deleteTransitionAction->handle($this->workflowTransition);
    }
}
