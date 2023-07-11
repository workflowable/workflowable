<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowSteps;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflowable\Actions\WorkflowSteps\CreateWorkflowStepAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowStepData;
use Workflowable\Workflowable\Exceptions\WorkflowStepException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowStepType;
use Workflowable\Workflowable\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflowable\Tests\TestCase;

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
        $workflowStepData = WorkflowStepData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_step_type_id' => $this->workflowStepType->id,
            'name' => 'Test Workflow Step',
            'description' => 'Test Workflow Step Description',
            'parameters' => [
                'test' => 'abc123',
            ],
            'ux_uuid' => 'test-uuid',
        ]);

        // Create a new workflow step using the action
        $action = new CreateWorkflowStepAction();
        $workflowStep = $action->handle($this->workflow, $workflowStepData);

        // Assert that the workflow step was created successfully
        $this->assertNotNull($workflowStep->id);
        $this->assertEquals($this->workflow->id, $workflowStep->workflow_id);
        $this->assertEquals($this->workflowStepType->id, $workflowStep->workflow_step_type_id);
        $this->assertEquals('abc123', $workflowStep->parameters()->where('key', 'test')->first()->value);
        $this->assertEquals('Test Workflow Step', $workflowStep->name);
        $this->assertEquals('Test Workflow Step Description', $workflowStep->description);
    }

    public function test_that_we_will_fail_when_providing_invalid_parameters()
    {
        $this->expectException(WorkflowStepException::class);
        $this->expectExceptionMessage(WorkflowStepException::workflowStepTypeParametersInvalid()->getMessage());

        // Create a new workflow step using the action
        $workflowStepData = WorkflowStepData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_step_type_id' => $this->workflowStepType->id,
            'name' => 'Test Workflow Step',
            'description' => 'Test Workflow Step Description',
            'parameters' => [
                'regex' => 'abc123',
            ],
            'ux_uuid' => 'test-uuid',
        ]);

        // Create a new workflow step using the action
        $action = new CreateWorkflowStepAction();
        $action->handle($this->workflow, $workflowStepData);
    }
}
