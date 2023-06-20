<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowSteps;

use Workflowable\Workflow\Actions\WorkflowSteps\DeleteWorkflowStepAction;
use Workflowable\Workflow\Exceptions\WorkflowException;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Tests\TestCase;
use Workflowable\Workflow\Tests\Traits\HasWorkflowRunTests;

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
