<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowActivityTypes;

use Workflowable\Workflowable\Actions\WorkflowActivityTypes\GetWorkflowActivityTypeImplementationAction;
use Workflowable\Workflowable\Exceptions\WorkflowActivityException;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\TestCase;

class GetWorkflowActivityTypeImplementationActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeFake::class,
        ]);
    }

    public function test_can_get_workflow_activity_type_implementation_by_alias(): void
    {
        /** @var GetWorkflowActivityTypeImplementationAction $action */
        $action = app(GetWorkflowActivityTypeImplementationAction::class);

        $workflowActivityTypeContract = $action->handle('workflow_activity_fake', [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowActivityTypeFake::class, $workflowActivityTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowActivityTypeContract->getParameters());
    }

    public function test_can_get_workflow_activity_type_implementation_by_id(): void
    {
        $workflowActivityType = WorkflowActivityType::factory()->withContract(new WorkflowActivityTypeFake())->create();

        /** @var GetWorkflowActivityTypeImplementationAction $action */
        $action = app(GetWorkflowActivityTypeImplementationAction::class);

        $workflowActivityTypeContract = $action->handle($workflowActivityType->id, [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowActivityTypeFake::class, $workflowActivityTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowActivityTypeContract->getParameters());
    }

    public function test_can_get_workflow_activity_type_implementation_by_workflow_activity_type_model(): void
    {
        $workflowActivityType = WorkflowActivityType::factory()->withContract(new WorkflowActivityTypeFake())->create();

        /** @var GetWorkflowActivityTypeImplementationAction $action */
        $action = app(GetWorkflowActivityTypeImplementationAction::class);

        $workflowActivityTypeContract = $action->handle($workflowActivityType, [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowActivityTypeFake::class, $workflowActivityTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowActivityTypeContract->getParameters());
    }

    public function test_throws_exception_if_workflow_activity_type_not_registered(): void
    {
        config()->set('workflowable.workflow_activity_types', []);

        /** @var GetWorkflowActivityTypeImplementationAction $action */
        $action = app(GetWorkflowActivityTypeImplementationAction::class);

        $this->expectException(WorkflowActivityException::class);
        $this->expectExceptionMessage(WorkflowActivityException::workflowActivityTypeNotRegistered()->getMessage());
        $action->handle('workflow_activity_fake', [
            'test' => 'Test',
        ]);
    }
}
