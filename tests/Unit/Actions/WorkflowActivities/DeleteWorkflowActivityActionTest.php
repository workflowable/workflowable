<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowActivities;

use Workflowable\Workflowable\Actions\WorkflowActivities\DeleteWorkflowActivityAction;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowRunTests;

class DeleteWorkflowActivityActionTest extends TestCase
{
    use HasWorkflowRunTests;

    public function test_that_we_can_delete_a_workflow_activity(): void
    {
        $this->workflow->update(['workflow_status_id' => WorkflowStatus::DRAFT]);

        /** @var DeleteWorkflowActivityAction $deleteWorkflowActivityAction */
        $deleteWorkflowActivityAction = app(DeleteWorkflowActivityAction::class);
        $deleteWorkflowActivityAction->handle($this->fromWorkflowActivity);

        $this->assertDatabaseMissing(WorkflowActivity::class, [
            'id' => $this->fromWorkflowActivity->id,
        ]);
    }

    public function test_that_we_cannot_delete_a_workflow_activity_from_an_active_workflow(): void
    {
        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotModifyWorkflowNotInDraftState()->getMessage());

        /** @var DeleteWorkflowActivityAction $deleteWorkflowActivityAction */
        $deleteWorkflowActivityAction = app(DeleteWorkflowActivityAction::class);
        $deleteWorkflowActivityAction->handle($this->fromWorkflowActivity);
    }
}
