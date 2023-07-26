<?php

namespace Workflowable\Workflowable\Tests\Unit\Traits;

use Illuminate\Support\Facades\Event;
use Workflowable\Workflowable\Events\Workflows\WorkflowActivated;
use Workflowable\Workflowable\Events\Workflows\WorkflowArchived;
use Workflowable\Workflowable\Events\Workflows\WorkflowDeactivated;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowConfigurationParameter;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunStatus;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasParameterConversions;
use Workflowable\Workflowable\Traits\InteractsWithWorkflows;

class InteractsWithWorkflowsTest extends TestCase
{
    use InteractsWithWorkflows;
    use HasParameterConversions;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupDefaultConversions();
    }

    /**
     * Test that an deactivated workflow can be activated successfully
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
            ->withWorkflowStatus(WorkflowStatus::DEACTIVATED)
            ->create();

        $result = $this->activateWorkflow($workflow);

        $this->assertEquals(WorkflowStatus::ACTIVE, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatus::ACTIVE,
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
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
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
            ->withWorkflowStatus(WorkflowStatus::DEACTIVATED)
            ->create();

        $result = $this->archiveWorkflow($workflow);

        $this->assertEquals(WorkflowStatus::ARCHIVED, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatus::ARCHIVED,
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
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
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
            ->withWorkflowStatus(WorkflowStatus::DEACTIVATED)
            ->create();

        // Create a new completed workflow run
        $workflowRun = WorkflowRun::factory()->withWorkflow($workflow)->create([
            'workflow_run_status_id' => WorkflowRunStatus::PENDING,
        ]);

        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage(WorkflowException::cannotArchiveWorkflowWithActiveRuns()->getMessage());

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
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $result = $this->deactivateWorkflow($workflow);

        $this->assertEquals(WorkflowStatus::DEACTIVATED, $result->workflow_status_id);
        $this->assertDatabaseHas('workflows', [
            'id' => $result->id,
            'workflow_status_id' => WorkflowStatus::DEACTIVATED,
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
            ->withWorkflowStatus(WorkflowStatus::DEACTIVATED)
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
            ->withWorkflowStatus(WorkflowStatus::DEACTIVATED)
            ->create();

        $workflow2 = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $result = $this->swapWorkflow($workflow2, $workflow1);

        $this->assertEquals($workflow1->id, $result->id);
        $this->assertEquals(WorkflowStatus::ACTIVE, $result->workflow_status_id);

        $this->assertDatabaseHas('workflows', [
            'id' => $workflow1->id,
            'workflow_status_id' => WorkflowStatus::ACTIVE,
        ]);

        $this->assertDatabaseHas('workflows', [
            'id' => $workflow2->id,
            'workflow_status_id' => WorkflowStatus::DEACTIVATED,
        ]);
    }

    public function test_that_we_can_clone_a_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
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

        collect([$fromWorkflowActivity, $toWorkflowActivity])->map(function ($workflowStep) use ($clonedWorkflow) {
            $this->assertDatabaseHas(WorkflowActivity::class, [
                'workflow_id' => $clonedWorkflow->id,
                'workflow_activity_type_id' => $workflowStep->workflow_activity_type_id,
                'ux_uuid' => $workflowStep->ux_uuid,
                'name' => $workflowStep->name,
                'description' => $workflowStep->description,
            ]);

            $clonedWorkflowStep = WorkflowActivity::query()
                ->where('ux_uuid', $workflowStep->ux_uuid)
                ->where('workflow_id', $clonedWorkflow->id)
                ->firstOrFail();

            foreach ($workflowStep->workflowConfigurationParameters as $parameter) {
                $this->assertDatabaseHas(WorkflowConfigurationParameter::class, [
                    'parameterizable_id' => $clonedWorkflowStep->id,
                    'parameterizable_type' => WorkflowActivity::class,
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
