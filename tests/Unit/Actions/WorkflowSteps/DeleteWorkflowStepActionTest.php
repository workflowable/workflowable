<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowSteps;

use Workflowable\Workflowable\Actions\WorkflowSteps\DeleteWorkflowStepAction;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowStep;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowRunTests;

class DeleteWorkflowStepActionTest extends TestCase
{
    use HasWorkflowRunTests;

    public function test_that_we_can_delete_a_workflow_step(): void
    {
        $this->workflow->update(['workflow_status_id' => WorkflowStatus::DRAFT]);

        /** @var DeleteWorkflowStepAction $deleteWorkflowStepAction */
        $deleteWorkflowStepAction = app(DeleteWorkflowStepAction::class);
        $deleteWorkflowStepAction->handle($this->fromWorkflowStep);

        $this->assertDatabaseMissing(WorkflowStep::class, [
            'id' => $this->fromWorkflowStep->id,
        ]);
    }

    public function test_that_we_cannot_delete_a_workflow_step_from_an_active_workflow(): void
    {
        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotModifyWorkflowNotInDraftState()->getMessage());

        /** @var DeleteWorkflowStepAction $deleteWorkflowStepAction */
        $deleteWorkflowStepAction = app(DeleteWorkflowStepAction::class);
        $deleteWorkflowStepAction->handle($this->fromWorkflowStep);
    }
}
