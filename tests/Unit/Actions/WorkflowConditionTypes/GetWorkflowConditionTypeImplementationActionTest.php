<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowConditionTypes;

use Workflowable\Workflowable\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\Workflowable\Exceptions\WorkflowConditionException;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\TestCase;

class GetWorkflowConditionTypeImplementationActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);
    }

    public function test_can_get_workflow_condition_type_implementation_by_alias(): void
    {
        /** @var GetWorkflowConditionTypeImplementationAction $action */
        $action = app(GetWorkflowConditionTypeImplementationAction::class);

        $workflowConditionTypeContract = $action->handle('workflow_condition_type_fake', [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowConditionTypeFake::class, $workflowConditionTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowConditionTypeContract->getParameters());
    }

    public function test_can_get_workflow_condition_type_implementation_by_id(): void
    {
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        /** @var GetWorkflowConditionTypeImplementationAction $action */
        $action = app(GetWorkflowConditionTypeImplementationAction::class);

        $workflowConditionTypeContract = $action->handle($workflowConditionType->id, [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowConditionTypeFake::class, $workflowConditionTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowConditionTypeContract->getParameters());
    }

    public function test_can_get_workflow_condition_type_implementation_by_workflow_condition_type_model(): void
    {
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        /** @var GetWorkflowConditionTypeImplementationAction $action */
        $action = app(GetWorkflowConditionTypeImplementationAction::class);

        $workflowConditionTypeContract = $action->handle($workflowConditionType, [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowConditionTypeFake::class, $workflowConditionTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowConditionTypeContract->getParameters());
    }

    public function test_throws_exception_if_workflow_activity_type_not_registered(): void
    {
        config()->set('workflowable.workflow_condition_types', []);

        /** @var GetWorkflowConditionTypeImplementationAction $action */
        $action = app(GetWorkflowConditionTypeImplementationAction::class);

        $this->expectException(WorkflowConditionException::class);
        $this->expectExceptionMessage(WorkflowConditionException::workflowConditionTypeNotRegistered()->getMessage());
        $action->handle('workflow_condition_fake', [
            'test' => 'Test',
        ]);
    }
}
