<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowSwaps;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Actions\WorkflowSwaps\ScheduleWorkflowSwapAction;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapScheduled;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowSwaps;

class ScheduleWorkflowSwapActionTest extends TestCase
{
    use HasWorkflowSwaps;

    public static function approved_statuses_for_scheduling_data_provider()
    {
        $testCases = [];
        foreach (WorkflowSwapStatusEnum::approvedForScheduling() as $key => $case) {
            $testCases[WorkflowSwapStatusEnum::label($case)] = [$case];
        }

        return $testCases;
    }

    /**
     * @return void
     *
     * @dataProvider approved_statuses_for_scheduling_data_provider
     */
    public function test_that_we_can_schedule_a_workflow_swap_for_processing(WorkflowSwapStatusEnum $statusEnum)
    {
        $this->workflowSwap->update([
            'workflow_swap_status_id' => $statusEnum,
        ]);

        Queue::fake();
        Event::fake();

        ScheduleWorkflowSwapAction::make()->handle($this->workflowSwap, now()->addDay()->startOfDay());

        Event::assertDispatched(function (WorkflowSwapScheduled $event) {
            return $this->workflowSwap->id === $event->workflowSwap->id;
        });

        $this->assertDatabaseHas(WorkflowSwap::class, [
            'id' => $this->workflowSwap->id,
            'workflow_swap_status_id' => WorkflowSwapStatusEnum::Scheduled,
            'scheduled_at' => now()->addDay()->startOfDay()->format('Y-m-d H:i:s'),
        ]);
    }

    public static function unapproved_statuses_for_scheduling_data_provider()
    {
        $testCases = [];
        foreach (WorkflowSwapStatusEnum::unapprovedForScheduling() as $key => $case) {
            $testCases[WorkflowSwapStatusEnum::label($case)] = [$case];
        }

        return $testCases;
    }

    /**
     * @return void
     *
     * @dataProvider unapproved_statuses_for_scheduling_data_provider
     */
    public function test_allowing_swap_scheduling_for_approved_statuses(WorkflowSwapStatusEnum $statusEnum)
    {
        $this->workflowSwap->update([
            'workflow_swap_status_id' => $statusEnum,
        ]);

        $this->expectException(WorkflowSwapException::class);
        $this->expectExceptionMessage(WorkflowSwapException::workflowSwapNotEligibleForScheduling()->getMessage());
        ScheduleWorkflowSwapAction::make()->handle($this->workflowSwap, now()->addDay());
    }
}
