<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowActivities;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflowable\Actions\WorkflowActivities\UpdateWorkflowActivityAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowActivityData;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\TestCase;

class UpdateWorkflowActivityActionTest extends TestCase
{
    use DatabaseTransactions;

    protected WorkflowEvent $workflowEvent;

    protected Workflow $workflow;

    protected WorkflowActivityType $workflowActivityType;

    protected WorkflowActivity $workflowActivity;

    public function setUp(): void
    {
        parent::setUp();

        $this->workflowEvent = WorkflowEvent::factory()->create();

        // Create a new workflow
        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeFake::class,
        ]);

        // Create a new workflow activity type
        $this->workflowActivityType = WorkflowActivityType::factory()->withContract(new WorkflowActivityTypeFake())->create();

        $this->workflowActivity = WorkflowActivity::factory()
            ->withWorkflow($this->workflow)
            ->withWorkflowActivityType($this->workflowActivityType)
            ->create();
    }

    public function test_can_create_workflow_activity_with_valid_parameters()
    {
        $workflowActivityData = WorkflowActivityData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_activity_type_id' => $this->workflowActivityType->id,
            'name' => 'Test Workflow Activity2',
            'description' => 'Test Workflow Activity Description2',
            'parameters' => [
                'test' => 'abc1234',
            ],
            'ux_uuid' => $this->workflowActivity->ux_uuid,
        ]);

        // Create a new workflow activity using the action
        $action = new UpdateWorkflowActivityAction();
        $workflowActivity = $action->handle($this->workflowActivity, $workflowActivityData);

        $workflowActivityParameter = $workflowActivity->workflowActivityParameters()->where('key', 'test')->first();
        // Assert that the workflow activity was created successfully
        $this->assertNotNull($workflowActivity->id);
        $this->assertEquals($this->workflow->id, $workflowActivity->workflow_id);
        $this->assertEquals($this->workflowActivityType->id, $workflowActivity->workflow_activity_type_id);
        $this->assertEquals('abc1234', $workflowActivityParameter->value);
    }

    public function test_that_we_will_fail_when_providing_invalid_parameters()
    {
        $workflowActivityData = WorkflowActivityData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_activity_type_id' => $this->workflowActivityType->id,
            'name' => 'Test Workflow Activity2',
            'description' => 'Test Workflow Activity Description2',
            'parameters' => [
                'regex' => 'abc1234',
            ],
            'ux_uuid' => $this->workflowActivity->ux_uuid,
        ]);

        $this->expectException(WorkflowActivityException::class);
        $this->expectExceptionMessage(WorkflowActivityException::workflowActivityTypeParametersInvalid()->getMessage());

        // Create a new workflow activity using the action
        $action = new UpdateWorkflowActivityAction();
        $action->handle($this->workflowActivity, $workflowActivityData);
    }
}
