<?php

namespace Workflowable\Workflow\Tests\Unit\Managers;

use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Managers\WorkflowStepTypeTypeManager;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class WorkflowStepManagerTest extends TestCase
{
    protected WorkflowStepTypeContract $dummyWorkflowStepType;

    public function setUp(): void
    {
        parent::setUp();

        $this->dummyWorkflowStepType = new WorkflowStepTypeFake();
    }

    public function test_that_we_can_register_a_workflow_step_type(): void
    {
        $workflowStepManager = new WorkflowStepTypeTypeManager();
        $workflowStepManager->register($this->dummyWorkflowStepType);

        $this->assertTrue($workflowStepManager->isRegistered('workflow_step_fake'));
    }

    public function test_that_we_can_get_a_workflow_action(): void
    {
        $workflowStepManager = new WorkflowStepTypeTypeManager();
        $workflowStepManager->register($this->dummyWorkflowStepType);

        $workflowAction = $workflowStepManager->getImplementation('workflow_step_fake');
        $this->assertInstanceOf(WorkflowStepTypeContract::class, $workflowAction);
        $this->assertEquals('Workflow Step Fake', $workflowAction->getFriendlyName());
    }

    public function test_that_we_can_get_the_rules_for_a_workflow_action(): void
    {
        $workflowStepManager = new WorkflowStepTypeTypeManager();
        $workflowStepManager->register($this->dummyWorkflowStepType);

        $rules = $workflowStepManager->getRules($this->dummyWorkflowStepType->getAlias());
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('test', $rules);
        $this->assertEquals('required', $rules['test']);
    }

    public function test_that_we_can_check_if_a_workflow_action_is_valid(): void
    {
        $workflowStepManager = new WorkflowStepTypeTypeManager();
        $workflowStepManager->register($this->dummyWorkflowStepType);

        $this->assertTrue($workflowStepManager->isValid($this->dummyWorkflowStepType->getAlias(), ['test' => 'test']));
        $this->assertFalse($workflowStepManager->isValid($this->dummyWorkflowStepType->getAlias(), []));
    }

    public function test_that_we_can_check_if_a_workflow_action_is_registered(): void
    {
        $workflowStepManager = new WorkflowStepTypeTypeManager();
        $workflowStepManager->register($this->dummyWorkflowStepType);

        $this->assertTrue($workflowStepManager->isRegistered($this->dummyWorkflowStepType->getAlias()));
        $this->assertFalse($workflowStepManager->isRegistered('test_workflow_step_2'));
    }
}
