<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowSteps;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflowable\Actions\WorkflowSteps\UpdateWorkflowStepAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowStepData;
use Workflowable\Workflowable\Exceptions\WorkflowStepException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowStep;
use Workflowable\Workflowable\Models\WorkflowStepType;
use Workflowable\Workflowable\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflowable\Tests\TestCase;

class UpdateWorkflowStepActionTest extends TestCase
{
    use DatabaseTransactions;

    protected WorkflowEvent $workflowEvent;

    protected Workflow $workflow;

    protected WorkflowStepType $workflowStepType;

    protected WorkflowStep $workflowStep;

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

        $this->workflowStep = WorkflowStep::factory()
            ->withWorkflow($this->workflow)
            ->withWorkflowStepType($this->workflowStepType)
            ->create();
    }

    public function test_can_create_workflow_step_with_valid_parameters()
    {
        $workflowStepData = WorkflowStepData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_step_type_id' => $this->workflowStepType->id,
            'name' => 'Test Workflow Step2',
            'description' => 'Test Workflow Step Description2',
            'parameters' => [
                'test' => 'abc1234',
            ],
            'ux_uuid' => $this->workflowStep->ux_uuid,
        ]);

        // Create a new workflow step using the action
        $action = new UpdateWorkflowStepAction();
        $workflowStep = $action->handle($this->workflowStep, $workflowStepData);

        // Assert that the workflow step was created successfully
        $this->assertNotNull($workflowStep->id);
        $this->assertEquals($this->workflow->id, $workflowStep->workflow_id);
        $this->assertEquals($this->workflowStepType->id, $workflowStep->workflow_step_type_id);
        $this->assertEquals('abc1234', $workflowStep->parameters()->where('key', 'test')->first()->value);
    }

    public function test_that_we_will_fail_when_providing_invalid_parameters()
    {
        $workflowStepData = WorkflowStepData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_step_type_id' => $this->workflowStepType->id,
            'name' => 'Test Workflow Step2',
            'description' => 'Test Workflow Step Description2',
            'parameters' => [
                'regex' => 'abc1234',
            ],
            'ux_uuid' => $this->workflowStep->ux_uuid,
        ]);

        $this->expectException(WorkflowStepException::class);
        $this->expectExceptionMessage(WorkflowStepException::workflowStepTypeParametersInvalid()->getMessage());

        // Create a new workflow step using the action
        $action = new UpdateWorkflowStepAction();
        $action->handle($this->workflowStep, $workflowStepData);
    }
}
