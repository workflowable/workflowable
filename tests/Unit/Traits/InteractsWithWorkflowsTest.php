<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Traits;

use Illuminate\Support\Facades\Event;
use Workflowable\WorkflowEngine\Events\Workflows\WorkflowActivated;
use Workflowable\WorkflowEngine\Events\Workflows\WorkflowArchived;
use Workflowable\WorkflowEngine\Events\Workflows\WorkflowDeactivated;
use Workflowable\WorkflowEngine\Exceptions\WorkflowException;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowEngineParameter;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowRun;
use Workflowable\WorkflowEngine\Models\WorkflowRunStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\WorkflowEngine\Tests\TestCase;
use Workflowable\WorkflowEngine\Traits\InteractsWithWorkflows;

class InteractsWithWorkflowsTest extends TestCase
{
    use InteractsWithWorkflows;

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

        $fromWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->withParameters()
            ->create();
        $toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->withParameters()
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStep)
            ->withToWorkflowStep($toWorkflowStep)
            ->create();

        $clonedWorkflow = $this->cloneWorkflow($workflow, 'Cloned Workflow');

        $this->assertEquals('Cloned Workflow', $clonedWorkflow->name);

        collect([$fromWorkflowStep, $toWorkflowStep])->map(function ($workflowStep) use ($clonedWorkflow) {
            $this->assertDatabaseHas(WorkflowStep::class, [
                'workflow_id' => $clonedWorkflow->id,
                'workflow_step_type_id' => $workflowStep->workflow_step_type_id,
                'ux_uuid' => $workflowStep->ux_uuid,
                'name' => $workflowStep->name,
                'description' => $workflowStep->description,
            ]);

            $clonedWorkflowStep = WorkflowStep::query()
                ->where('ux_uuid', $workflowStep->ux_uuid)
                ->where('workflow_id', $clonedWorkflow->id)
                ->firstOrFail();

            foreach ($workflowStep->parameters as $parameter) {
                $this->assertDatabaseHas(WorkflowEngineParameter::class, [
                    'parameterizable_id' => $clonedWorkflowStep->id,
                    'parameterizable_type' => WorkflowStep::class,
                    'key' => $parameter->key,
                    'value' => $parameter->value,
                ]);
            }
        });

        $transitionWasCloned = WorkflowTransition::query()
            ->whereHas('fromWorkflowStep', function ($query) use ($fromWorkflowStep) {
                $query->where('ux_uuid', $fromWorkflowStep->ux_uuid);
            })
            ->whereHas('toWorkflowStep', function ($query) use ($toWorkflowStep) {
                $query->where('ux_uuid', $toWorkflowStep->ux_uuid);
            })
            ->where('workflow_id', $clonedWorkflow->id)
            ->where('name', $workflowTransition->name)
            ->where('ux_uuid', $workflowTransition->ux_uuid)
            ->where('ordinal', $workflowTransition->ordinal)
            ->exists();

        $this->assertTrue($transitionWasCloned);
    }
}