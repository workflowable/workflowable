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
        $workflowConditionTypeContract = GetWorkflowConditionTypeImplementationAction::make()->handle('workflow_condition_type_fake');

        $this->assertInstanceOf(WorkflowConditionTypeFake::class, $workflowConditionTypeContract);
    }

    public function test_can_get_workflow_condition_type_implementation_by_id(): void
    {
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        $workflowConditionTypeContract = GetWorkflowConditionTypeImplementationAction::make()->handle($workflowConditionType->id);

        $this->assertInstanceOf(WorkflowConditionTypeFake::class, $workflowConditionTypeContract);
    }

    public function test_can_get_workflow_condition_type_implementation_by_workflow_condition_type_model(): void
    {
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        $workflowConditionTypeContract = GetWorkflowConditionTypeImplementationAction::make()->handle($workflowConditionType);

        $this->assertInstanceOf(WorkflowConditionTypeFake::class, $workflowConditionTypeContract);
    }

    public function test_throws_exception_if_workflow_activity_type_not_registered(): void
    {
        config()->set('workflowable.workflow_condition_types', []);

        $this->expectException(WorkflowConditionException::class);
        $this->expectExceptionMessage(WorkflowConditionException::workflowConditionTypeNotRegistered()->getMessage());
        GetWorkflowConditionTypeImplementationAction::make()->handle('workflow_condition_fake');
    }
}
