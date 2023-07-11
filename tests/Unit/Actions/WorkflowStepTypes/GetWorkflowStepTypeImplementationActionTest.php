<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowStepTypes;

use Workflowable\Workflowable\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflowable\Exceptions\WorkflowStepException;
use Workflowable\Workflowable\Models\WorkflowStepType;
use Workflowable\Workflowable\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflowable\Tests\TestCase;

class GetWorkflowStepTypeImplementationActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_step_types', [
            WorkflowStepTypeFake::class,
        ]);
    }

    public function test_can_get_workflow_step_type_implementation_by_alias(): void
    {
        /** @var GetWorkflowStepTypeImplementationAction $action */
        $action = app(GetWorkflowStepTypeImplementationAction::class);

        $workflowStepTypeContract = $action->handle('workflow_step_fake', [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowStepTypeFake::class, $workflowStepTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowStepTypeContract->getParameters());
    }

    public function test_can_get_workflow_step_type_implementation_by_id(): void
    {
        $workflowStepType = WorkflowStepType::factory()->withContract(new WorkflowStepTypeFake())->create();

        /** @var GetWorkflowStepTypeImplementationAction $action */
        $action = app(GetWorkflowStepTypeImplementationAction::class);

        $workflowStepTypeContract = $action->handle($workflowStepType->id, [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowStepTypeFake::class, $workflowStepTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowStepTypeContract->getParameters());
    }

    public function test_can_get_workflow_step_type_implementation_by_workflow_step_type_model(): void
    {
        $workflowStepType = WorkflowStepType::factory()->withContract(new WorkflowStepTypeFake())->create();

        /** @var GetWorkflowStepTypeImplementationAction $action */
        $action = app(GetWorkflowStepTypeImplementationAction::class);

        $workflowStepTypeContract = $action->handle($workflowStepType, [
            'test' => 'Test',
        ]);

        $this->assertInstanceOf(WorkflowStepTypeFake::class, $workflowStepTypeContract);
        $this->assertEqualsCanonicalizing(['test' => 'Test'], $workflowStepTypeContract->getParameters());
    }

    public function test_throws_exception_if_workflow_step_type_not_registered(): void
    {
        config()->set('workflowable.workflow_step_types', []);

        /** @var GetWorkflowStepTypeImplementationAction $action */
        $action = app(GetWorkflowStepTypeImplementationAction::class);

        $this->expectException(WorkflowStepException::class);
        $this->expectExceptionMessage(WorkflowStepException::workflowStepTypeNotRegistered()->getMessage());
        $action->handle('workflow_step_fake', [
            'test' => 'Test',
        ]);
    }
}
