<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowActivities;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflowable\Actions\WorkflowActivities\SaveWorkflowActivityAction;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\GetWorkflowActivityTypeImplementationAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowActivityData;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\TestCase;

class SaveWorkflowActivityActionTest extends TestCase
{
    use DatabaseTransactions;

    protected WorkflowEvent $workflowEvent;

    protected Workflow $workflow;

    protected WorkflowActivityType $workflowActivityType;

    public function setUp(): void
    {
        parent::setUp();

        $this->workflowEvent = WorkflowEvent::factory()->create();

        // Create a new workflow
        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeFake::class,
        ]);

        // Create a new workflow activity type
        $this->workflowActivityType = WorkflowActivityType::factory()->withContract(new WorkflowActivityTypeFake())->create();
    }

    public function test_can_create_workflow_activity_with_valid_parameters()
    {
        $workflowActivityData = WorkflowActivityData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_activity_type_id' => $this->workflowActivityType->id,
            'name' => 'Test Workflow Activity',
            'description' => 'Test Workflow Activity Description',
            'parameters' => [
                'test' => 'abc123',
            ],
            'ux_uuid' => 'test-uuid',
        ]);

        // Create a new workflow activity using the action
        $action = new SaveWorkflowActivityAction();
        $workflowActivity = $action->handle($this->workflow, $workflowActivityData);

        $workflowActivityParameter = $workflowActivity->workflowActivityParameters()->where('key', 'test')->first();

        // Assert that the workflow activity was created successfully
        $this->assertNotNull($workflowActivity->id);
        $this->assertEquals($this->workflow->id, $workflowActivity->workflow_id);
        $this->assertEquals($this->workflowActivityType->id, $workflowActivity->workflow_activity_type_id);
        $this->assertEquals('abc123', $workflowActivityParameter->value);
        $this->assertEquals('Test Workflow Activity', $workflowActivity->name);
        $this->assertEquals('Test Workflow Activity Description', $workflowActivity->description);
    }

    public function test_that_we_will_fail_when_providing_invalid_parameters()
    {
        $activityTypeMock = $this->partialMock(WorkflowActivityTypeFake::class, function ($mock) {
            $mock->shouldReceive('hasValidParameters')->andReturn(false);
        });

        $this->partialMock(GetWorkflowActivityTypeImplementationAction::class, function ($mock) use ($activityTypeMock) {
            $mock->shouldReceive('handle')->andReturn($activityTypeMock);
        });

        $this->expectException(WorkflowActivityException::class);
        $this->expectExceptionMessage(WorkflowActivityException::workflowActivityTypeParametersInvalid()->getMessage());

        // Create a new workflow activity using the action
        $workflowActivityData = WorkflowActivityData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_activity_type_id' => $this->workflowActivityType->id,
            'name' => 'Test Workflow Activity',
            'description' => 'Test Workflow Activity Description',
            'parameters' => [
                'regex' => 'abc123',
            ],
            'ux_uuid' => 'test-uuid',
        ]);

        // Create a new workflow activity using the action
        $action = new SaveWorkflowActivityAction();
        $action->handle($this->workflow, $workflowActivityData);
    }

    public function test_that_we_can_update_an_existing_workflow_activity()
    {
        $this->markTestIncomplete('Not written yet');
    }
}
