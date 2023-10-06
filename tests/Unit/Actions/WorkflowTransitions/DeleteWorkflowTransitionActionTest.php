<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowTransitions;

use Workflowable\Workflowable\Actions\WorkflowTransitions\DeleteWorkflowTransitionAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcessTests;

class DeleteWorkflowTransitionActionTest extends TestCase
{
    use HasWorkflowProcessTests;

    public function test_that_we_can_delete_a_workflow_transition(): void
    {
        $this->workflow->update(['workflow_status_id' => WorkflowStatusEnum::DRAFT]);

        DeleteWorkflowTransitionAction::make()->handle($this->workflowTransition);
        $this->assertDatabaseMissing(WorkflowTransition::class, [
            'id' => $this->workflowTransition->id,
        ]);
    }

    public function test_that_we_can_cannot_delete_a_workflow_transition_from_a_active_workflow(): void
    {
        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotModifyWorkflowNotInDraftState()->getMessage());
        DeleteWorkflowTransitionAction::make()->handle($this->workflowTransition);
    }
}
