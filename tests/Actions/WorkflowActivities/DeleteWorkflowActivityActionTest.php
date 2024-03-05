<?php

namespace Workflowable\Workflowable\Tests\Actions\WorkflowActivities;

use Workflowable\Workflowable\Actions\WorkflowActivities\DeleteWorkflowActivityAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class DeleteWorkflowActivityActionTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_delete_a_workflow_activity(): void
    {
        $this->workflow->update(['workflow_status_id' => WorkflowStatusEnum::DRAFT]);

        DeleteWorkflowActivityAction::make()->handle($this->fromWorkflowActivity);

        $this->assertDatabaseMissing(WorkflowActivity::class, [
            'id' => $this->fromWorkflowActivity->id,
        ]);
    }

    public function test_that_we_cannot_delete_a_workflow_activity_from_an_active_workflow(): void
    {
        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowNotEditable()->getMessage());

        DeleteWorkflowActivityAction::make()->handle($this->fromWorkflowActivity);
    }
}
