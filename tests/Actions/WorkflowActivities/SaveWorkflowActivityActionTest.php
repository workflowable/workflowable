<?php

namespace Workflowable\Workflowable\Tests\Actions\WorkflowActivities;

use Workflowable\Workflowable\Actions\WorkflowActivities\SaveWorkflowActivityAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowActivityData;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\InvalidWorkflowParametersException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityParameter;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class SaveWorkflowActivityActionTest extends TestCase
{
    use HasWorkflowProcess;

    protected WorkflowEvent $workflowEvent;

    protected Workflow $workflow;

    protected WorkflowActivityType $workflowActivityType;

    public function test_can_create_workflow_activity_with_valid_parameters()
    {
        $this->workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        // Create a new workflow
        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        // Create a new workflow activity type
        $this->workflowActivityType = WorkflowActivityType::query()->where('class_name', WorkflowActivityTypeFake::class)->firstOrFail();

        $workflowActivityData = WorkflowActivityData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_activity_type_id' => $this->workflowActivityType->id,
            'name' => 'Test Workflow Activity',
            'description' => 'Test Workflow Activity Description',
            'parameters' => [
                'test' => 'abc123',
            ],
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
        $this->workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        // Create a new workflow
        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        // Create a new workflow activity type
        $this->workflowActivityType = WorkflowActivityType::query()->where('class_name', WorkflowActivityTypeFake::class)->firstOrFail();

        $workflowActivityData = WorkflowActivityData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_activity_type_id' => $this->workflowActivityType->id,
            'name' => 'Test Workflow Activity',
            'description' => 'Test Workflow Activity Description',
            'parameters' => [],
        ]);

        // Create a new workflow activity using the action
        $action = new SaveWorkflowActivityAction();
        $this->expectException(InvalidWorkflowParametersException::class);
        $action->handle($this->workflow, $workflowActivityData);
    }

    public function test_that_we_can_update_an_existing_workflow_activity()
    {
        $this->workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        // Create a new workflow
        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        // Create a new workflow activity type
        $this->workflowActivityType = WorkflowActivityType::query()->where('class_name', WorkflowActivityTypeFake::class)->firstOrFail();

        $workflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType($this->workflowActivityType)
            ->withWorkflow($this->workflow)
            ->create();

        $workflowActivityData = WorkflowActivityData::fromArray([
            'workflow_id' => $this->workflow->id,
            'workflow_activity_type_id' => $this->workflowActivityType->id,
            'name' => 'Test Workflow Activity',
            'description' => 'Test Workflow Activity Description',
            'parameters' => [
                'test' => 'Updated Parameter',
            ],
        ]);

        SaveWorkflowActivityAction::make()
            ->withWorkflowActivity($workflowActivity)
            ->handle($this->workflow, $workflowActivityData);

        $this->assertDatabaseHas(WorkflowActivity::class, [
            'workflow_id' => $this->workflow->id,
            'workflow_activity_type_id' => $this->workflowActivityType->id,
            'name' => 'Test Workflow Activity',
            'description' => 'Test Workflow Activity Description',
        ]);

        $this->assertDatabaseHas(WorkflowActivityParameter::class, [
            'key' => 'test',
            'value' => 'Updated Parameter',
            'workflow_activity_id' => $workflowActivity->id,
        ]);
    }
}
