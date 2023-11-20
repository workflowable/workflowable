<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowSwaps;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Actions\WorkflowSwaps\OutstandingWorkflowProcessSwapAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCancelled;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowSwapActivityMap;
use Workflowable\Workflowable\Models\WorkflowSwapAuditLog;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowSwaps;

class OutstandingWorkflowProcessSwapActionTest extends TestCase
{
    use HasWorkflowSwaps;

    public function test_that_the_from_workflow_process_will_be_cancelled()
    {
        Event::fake();

        $workflowSwapAuditLog = OutstandingWorkflowProcessSwapAction::make()->handle($this->workflowSwap, $this->workflowProcess);

        Event::assertDispatched(function (WorkflowProcessCancelled $processCancelled) {
            return $processCancelled->workflowProcess->id === $this->workflowProcess->id;
        });

        $this->assertDatabaseHas(WorkflowProcess::class, [
            'id' => $this->workflowProcess->id,
            'workflow_process_status_id' => WorkflowProcessStatusEnum::CANCELLED,
        ]);

        $this->assertDatabaseHas(WorkflowSwapAuditLog::class, [
            'id' => $workflowSwapAuditLog->id,
            'from_workflow_process_id' => $this->workflowProcess->id,
        ]);
    }

    public function test_we_will_create_a_workflow_process_at_the_correct_position_when_to_activity_is_identified()
    {
        Event::fake();

        $workflowSwapAuditLog = OutstandingWorkflowProcessSwapAction::make()->handle($this->workflowSwap, $this->workflowProcess);

        // The process was created, it's pending and it's mapped to the correct workflow activity from the mapping
        $this->assertDatabaseHas(WorkflowProcess::class, [
            'workflow_process_status_id' => WorkflowProcessStatusEnum::CREATED,
            'last_workflow_activity_id' => $this->workflowSwapActivityMapOne->to_workflow_activity_id,
        ]);

        $this->assertDatabaseHas(WorkflowSwapAuditLog::class, [
            'id' => $workflowSwapAuditLog->id,
            'from_workflow_process_id' => $this->workflowProcess->id,
            'from_workflow_activity_id' => $this->workflowProcess->last_workflow_activity_id,
            'to_workflow_activity_id' => $this->workflowSwapActivityMapOne->to_workflow_activity_id,
        ]);
    }

    public function test_we_will_create_a_fresh_workflow_process_when_no_to_workflow_activity_is_identified()
    {
        $this->workflowSwapActivityMapOne->update([
            'to_workflow_activity_id' => null,
        ]);

        $workflowSwapAuditLog = OutstandingWorkflowProcessSwapAction::make()->handle($this->workflowSwap, $this->workflowProcess);

        // The process was created, it's pending, and it's mapped to the correct workflow activity from the mapping
        $this->assertDatabaseHas(WorkflowProcess::class, [
            'workflow_process_status_id' => WorkflowProcessStatusEnum::CREATED,
            'last_workflow_activity_id' => null,
        ]);

        $this->assertDatabaseHas(WorkflowSwapAuditLog::class, [
            'id' => $workflowSwapAuditLog->id,
            'from_workflow_process_id' => $this->workflowProcess->id,
            'from_workflow_activity_id' => $this->workflowProcess->last_workflow_activity_id,
            'to_workflow_activity_id' => null,
        ]);
    }

    public function test_that_when_no_mapping_exists_an_exception_will_be_thrown()
    {
        WorkflowSwapActivityMap::query()->delete();
        $this->workflowSwap->fresh();

        $this->expectException(WorkflowSwapException::class);
        $this->expectExceptionMessage(WorkflowSwapException::missingWorkflowSwapActivityMap()->getMessage());
        OutstandingWorkflowProcessSwapAction::make()->handle($this->workflowSwap, $this->workflowProcess);
    }

    public function test_that_we_will_correctly_port_workflow_process_import_tokens()
    {
        $this->markTestIncomplete('Not written yet');
    }
}
