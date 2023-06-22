<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\WorkflowSteps;

use Workflowable\WorkflowEngine\Actions\WorkflowSteps\DeleteWorkflowStepAction;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Tests\TestCase;
use Workflowable\WorkflowEngine\Tests\Traits\HasWorkflowRunTests;

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
