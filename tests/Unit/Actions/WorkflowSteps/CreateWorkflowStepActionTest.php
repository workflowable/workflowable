<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowSteps;

use Workflowable\Workflow\Actions\WorkflowSteps\CreateWorkflowStepAction;
use Workflowable\Workflow\Contracts\WorkflowEventManagerContract;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Managers\WorkflowStepTypeManager;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStepType;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class CreateWorkflowStepActionTest extends TestCase
{
    protected WorkflowEvent $workflowEvent;

    protected Workflow $workflow;

    protected WorkflowStepType $workflowStepType;

    public function setUp(): void
    {
        parent::setUp();

        $this->workflowEvent = WorkflowEvent::factory()->create();

        // Create a new workflow
        $this->workflow = Workflow::factory()->withWorkflowEvent($this->workflowEvent)->withWorkflowStatus(WorkflowStatus::ACTIVE)->create();

        // Create a new workflow step type
        $this->workflowStepType = WorkflowStepType::factory()->withContract(new WorkflowStepTypeFake())->create();

        app()->singleton(WorkflowEventManagerContract::class, function () {
            $manager = new WorkflowStepTypeManager();

            $manager->register(new WorkflowStepTypeFake());

            return $manager;
        });
    }

    public function testCanCreateWorkflowStep()
    {
        // Create a new workflow step using the action
        $action = new CreateWorkflowStepAction();
        $workflowStep = $action->handle($this->workflow, $this->workflowStepType);

        // Assert that the workflow step was created successfully
        $this->assertNotNull($workflowStep->id);
        $this->assertEquals($this->workflow->id, $workflowStep->workflow_id);
        $this->assertEquals($this->workflowStepType->id, $workflowStep->workflow_step_type_id);
    }

    public function testCanCreateWorkflowStepWithParameters()
    {
        $manager = app(WorkflowStepTypeManager::class);
        dd($manager->getImplementations());

        // Create a new workflow step using the action
        $action = new CreateWorkflowStepAction();
        $workflowStep = $action->handle($workflow, $workflowStepType, [
            'test' => 'abc123',
        ]);

        // Assert that the workflow step was created successfully
        $this->assertNotNull($workflowStep->id);
        $this->assertEquals($workflow->id, $workflowStep->workflow_id);
        $this->assertEquals($workflowStepType->id, $workflowStep->workflow_step_type_id);
        $this->assertEquals('abc123', $workflowStep->parameters['test']);
    }

    public function testThrowsExceptionIfWorkflowStepTypeIsNotRegistered()
    {
        $workflowEvent = WorkflowEvent::factory()->create();

        // Create a new workflow
        $workflow = Workflow::factory()->withWorkflowEvent($workflowEvent)->withWorkflowStatus(WorkflowStatus::ACTIVE)->create();

        // Create a new workflow step type
        $workflowStepType = WorkflowStepType::factory()->create([
            'alias' => 'unregistered_step_type',
        ]);

        // Create a new workflow step using the action
        $action = new CreateWorkflowStepAction();

        // Assert that an exception is thrown if the workflow step type is not registered
        $this->expectException(WorkflowStepException::class);
        $this->expectExceptionMessage(WorkflowStepException::workflowStepTypeNotRegistered('unregistered_step_type')->getMessage());
        $action->handle($workflow, $workflowStepType);
    }
}
