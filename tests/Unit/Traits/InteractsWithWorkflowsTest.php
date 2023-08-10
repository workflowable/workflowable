<?php

namespace Workflowable\Workflowable\Tests\Unit\Traits;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Events\Workflows\WorkflowActivated;
use Workflowable\Workflowable\Events\Workflows\WorkflowArchived;
use Workflowable\Workflowable\Events\Workflows\WorkflowDeactivated;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityParameter;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Concerns\InteractsWithWorkflows;

class InteractsWithWorkflowsTest extends TestCase
{
    use InteractsWithWorkflows;

    /**
     * Test that a deactivated workflow can be activated successfully
     */
    public function test_can_activate_deactivated_workflow(): void
    {
        Event::fake();
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED)
            ->create();

        $result = $this->activateWorkflow($workflow);

        $this->assertEquals(WorkflowStatusEnum::ACTIVE, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatusEnum::ACTIVE,
        ]);

        Event::assertDispatched(WorkflowActivated::class, function ($event) use ($result) {
            return $event->workflow->id === $result->id;
        });
    }

    /**
     * Test that an active workflow cannot be activated again
     */
    public function test_cannot_activate_already_active_workflow(): void
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowAlreadyActive()->getMessage());

        $this->activateWorkflow($workflow);
    }

    /**
     * Test that a deactivated workflow can be archived successfully
     */
    public function test_can_archive_deactivated_workflow(): void
    {
        Event::fake();

        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED)
            ->create();

        $result = $this->archiveWorkflow($workflow);

        $this->assertEquals(WorkflowStatusEnum::ARCHIVED, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatusEnum::ARCHIVED,
        ]);

        Event::assertDispatched(WorkflowArchived::class, function ($event) use ($result) {
            return $event->workflow->id === $result->id;
        });
    }

    /**
     * Test that a workflow cannot be archived if it is active
     */
    public function test_cannot_archive_active_workflow(): void
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowCannotBeArchivedFromActiveState()->getMessage());

        $this->archiveWorkflow($workflow);
    }

    /**
     * Test that a workflow cannot be archived if it has active runs
     */
    public function test_cannot_archive_workflow_with_active_runs(): void
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED->value)
            ->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowProcess::factory()->withWorkflow($workflow)->create([
            'workflow_process_status_id' => WorkflowProcessStatusEnum::PENDING,
        ]);

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotArchiveWorkflowWithActiveProcesses()->getMessage());

        $this->archiveWorkflow($workflow);
    }

    /**
     * Test that an active workflow can be deactivated successfully
     *
     * @throws WorkflowException
     */
    public function test_can_deactivate_active_workflow(): void
    {
        Event::fake();
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $result = $this->deactivateWorkflow($workflow);

        $this->assertEquals(WorkflowStatusEnum::DEACTIVATED, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatusEnum::DEACTIVATED,
        ]);

        Event::assertDispatched(WorkflowDeactivated::class, function ($event) use ($result) {
            return $event->workflow->id === $result->id;
        });
    }

    /**
     * Test that a deactivated workflow cannot be deactivated again
     */
    public function test_cannot_deactivate_already_deactivated_workflow(): void
    {
        Event::fake();
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED)
            ->create();

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::workflowAlreadyDeactivated()->getMessage());

        $this->deactivateWorkflow($workflow);
    }

    /**
     * Test that swapping active workflows works as expected
     *
     * @return void
     */
    public function test_swap_active_workflows()
    {
        /** @var WorkflowEvent $workflowEvent */
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake([
            'test' => 'test',
        ]))->create();

        $workflow1 = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DEACTIVATED)
            ->create();

        $workflow2 = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $result = $this->swapWorkflow($workflow2, $workflow1);

        $this->assertEquals($workflow1->id, $result->id);
        $this->assertEquals(WorkflowStatusEnum::ACTIVE, $result->workflow_status_id);

        $this->assertDatabaseHas('workflows', [
            'id' => $workflow1->id,
            'workflow_status_id' => WorkflowStatusEnum::ACTIVE,
        ]);

        $this->assertDatabaseHas('workflows', [
            'id' => $workflow2->id,
            'workflow_status_id' => WorkflowStatusEnum::DEACTIVATED,
        ]);
    }

    public function test_that_we_can_clone_a_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->withParameters()
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->withParameters()
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

        $clonedWorkflow = $this->cloneWorkflow($workflow, 'Cloned Workflow');

        $this->assertEquals('Cloned Workflow', $clonedWorkflow->name);

        collect([$fromWorkflowActivity, $toWorkflowActivity])->map(function ($workflowActivity) use ($clonedWorkflow) {
            $this->assertDatabaseHas(WorkflowActivity::class, [
                'workflow_id' => $clonedWorkflow->id,
                'workflow_activity_type_id' => $workflowActivity->workflow_activity_type_id,
                'ux_uuid' => $workflowActivity->ux_uuid,
                'name' => $workflowActivity->name,
                'description' => $workflowActivity->description,
            ]);

            $clonedWorkflowActivity = WorkflowActivity::query()
                ->where('ux_uuid', $workflowActivity->ux_uuid)
                ->where('workflow_id', $clonedWorkflow->id)
                ->firstOrFail();

            foreach ($workflowActivity->workflowActivityParameters as $parameter) {
                $this->assertDatabaseHas(WorkflowActivityParameter::class, [
                    'workflow_activity_id' => $clonedWorkflowActivity->id,
                    'key' => $parameter->key,
                    'value' => $parameter->value,
                ]);
            }
        });

        $transitionWasCloned = WorkflowTransition::query()
            ->whereHas('fromWorkflowActivity', function ($query) use ($fromWorkflowActivity) {
                $query->where('ux_uuid', $fromWorkflowActivity->ux_uuid);
            })
            ->whereHas('toWorkflowActivity', function ($query) use ($toWorkflowActivity) {
                $query->where('ux_uuid', $toWorkflowActivity->ux_uuid);
            })
            ->where('workflow_id', $clonedWorkflow->id)
            ->where('name', $workflowTransition->name)
            ->where('ux_uuid', $workflowTransition->ux_uuid)
            ->where('ordinal', $workflowTransition->ordinal)
            ->exists();

        $this->assertTrue($transitionWasCloned);
    }
}
