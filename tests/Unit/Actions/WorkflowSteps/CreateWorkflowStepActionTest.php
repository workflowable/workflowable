<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowSteps;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflow\Actions\WorkflowSteps\CreateWorkflowStepAction;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStepType;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class CreateWorkflowStepActionTest extends TestCase
{
    use DatabaseTransactions;

    protected WorkflowEvent $workflowEvent;

    protected Workflow $workflow;

    protected WorkflowStepType $workflowStepType;

    public function setUp(): void
    {
        parent::setUp();

        $this->workflowEvent = WorkflowEvent::factory()->create();

        // Create a new workflow
        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        config()->set('workflowable.workflow_step_types', [
            WorkflowStepTypeFake::class,
        ]);

        // Create a new workflow step type
        $this->workflowStepType = WorkflowStepType::factory()->withContract(new WorkflowStepTypeFake())->create();
    }

    public function test_can_create_workflow_step_with_valid_parameters()
    {
        // Create a new workflow step using the action
        $action = new CreateWorkflowStepAction();
        $workflowStep = $action->handle($this->workflow, $this->workflowStepType, [
            'test' => 'abc123',
        ]);

        // Assert that the workflow step was created successfully
        $this->assertNotNull($workflowStep->id);
        $this->assertEquals($this->workflow->id, $workflowStep->workflow_id);
        $this->assertEquals($this->workflowStepType->id, $workflowStep->workflow_step_type_id);
        $this->assertEquals('abc123', $workflowStep->parameters['test']);
    }

    public function test_that_we_will_fail_when_providing_invalid_parameters()
    {
        $this->expectException(WorkflowStepException::class);
        $this->expectExceptionMessage(WorkflowStepException::workflowStepTypeParametersInvalid()->getMessage());

        // Create a new workflow step using the action
        $action = new CreateWorkflowStepAction();
        $action->handle($this->workflow, $this->workflowStepType, [
            'regex' => 'abc123',
        ]);
    }
}
