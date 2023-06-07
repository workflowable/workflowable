<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowSteps;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflow\Actions\WorkflowSteps\UpdateWorkflowStepAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowStepData;
use Workflowable\Workflow\Exceptions\WorkflowStepException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowStepType;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

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

        config()->set('workflow-engine.workflow_step_types', [
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
        $this->assertEquals('abc1234', $workflowStep->parameters['test']);
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
