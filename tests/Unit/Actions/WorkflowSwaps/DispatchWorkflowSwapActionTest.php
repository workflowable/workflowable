<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowSwaps;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Workflowable\Workflowable\Actions\WorkflowSwaps\DispatchWorkflowSwapAction;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Events\WorkflowSwaps\WorkflowSwapDispatched;
use Workflowable\Workflowable\Exceptions\WorkflowSwapException;
use Workflowable\Workflowable\Jobs\WorkflowSwapRunnerJob;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowSwaps;

class DispatchWorkflowSwapActionTest extends TestCase
{
    use HasWorkflowSwaps;

    public static function approved_statuses_for_dispatching_data_provider()
    {
        $testCases = [];
        foreach (WorkflowSwapStatusEnum::approvedForDispatch() as $key => $case) {
            $testCases[WorkflowSwapStatusEnum::label($case)] = [$case];
        }

        return $testCases;
    }

    /**
     * @return void
     *
     * @dataProvider approved_statuses_for_dispatching_data_provider
     */
    public function test_that_we_can_dispatch_a_workflow_swap_for_processing(WorkflowSwapStatusEnum $statusEnum)
    {
        Queue::fake();
        Event::fake();
        $this->travelTo(now()->startOfDay());

        $this->workflowSwap->update([
            'workflow_swap_status_id' => $statusEnum,
        ]);

        DispatchWorkflowSwapAction::make()->handle($this->workflowSwap);

        Event::assertDispatched(function (WorkflowSwapDispatched $event) {
            return $this->workflowSwap->id === $event->workflowSwap->id;
        });

        Queue::assertPushed(function (WorkflowSwapRunnerJob $job) {
            return $this->workflowSwap->id === $job->workflowSwap->id;
        });
        $this->assertDatabaseHas(WorkflowSwap::class, [
            'id' => $this->workflowSwap->id,
            'workflow_swap_status_id' => WorkflowSwapStatusEnum::Dispatched,
            'dispatched_at' => now()->startOfDay()->format('Y-m-d H:i:s'),
        ]);
    }

    public static function unapproved_statuses_for_dispatching_data_provider()
    {
        $testCases = [];
        foreach (WorkflowSwapStatusEnum::unapprovedForDispatch() as $key => $case) {
            $testCases[WorkflowSwapStatusEnum::label($case)] = [$case];
        }

        return $testCases;
    }

    /**
     * @return void
     *
     * @dataProvider unapproved_statuses_for_dispatching_data_provider
     */
    public function test_allowing_swap_dispatching_for_approved_statuses(WorkflowSwapStatusEnum $statusEnum)
    {
        $this->workflowSwap->update([
            'workflow_swap_status_id' => $statusEnum,
        ]);

        $this->expectException(WorkflowSwapException::class);
        $this->expectExceptionMessage(WorkflowSwapException::workflowSwapNotEligibleForDispatch()->getMessage());
        DispatchWorkflowSwapAction::make()->handle($this->workflowSwap);
    }
}
