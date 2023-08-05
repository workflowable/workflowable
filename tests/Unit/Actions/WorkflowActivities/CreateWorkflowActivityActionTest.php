<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowActivities;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Workflowable\Workflowable\Actions\WorkflowActivities\CreateWorkflowActivityAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowActivityData;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasParameterConversions;

class CreateWorkflowActivityActionTest extends TestCase
{
    use DatabaseTransactions;
    use HasParameterConversions;

    protected WorkflowEvent $workflowEvent;

    protected Workflow $workflow;

    protected WorkflowActivityType $workflowActivityType;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupDefaultConversions();

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
        $action = new CreateWorkflowActivityAction();
        $workflowActivity = $action->handle($this->workflow, $workflowActivityData);

        $workflowConfigurationParameter = $workflowActivity->workflowActivityParameters()->where('key', 'test')->first();

        // Assert that the workflow activity was created successfully
        $this->assertNotNull($workflowActivity->id);
        $this->assertEquals($this->workflow->id, $workflowActivity->workflow_id);
        $this->assertEquals($this->workflowActivityType->id, $workflowActivity->workflow_activity_type_id);
        $this->assertEquals('abc123', $workflowConfigurationParameter->value);
        $this->assertEquals('Test Workflow Activity', $workflowActivity->name);
        $this->assertEquals('Test Workflow Activity Description', $workflowActivity->description);
    }

    public function test_that_we_will_fail_when_providing_invalid_parameters()
    {
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
        $action = new CreateWorkflowActivityAction();
        $action->handle($this->workflow, $workflowActivityData);
    }
}
