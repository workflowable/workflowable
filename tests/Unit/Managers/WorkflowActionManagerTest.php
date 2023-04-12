<?php

namespace Workflowable\Workflow\Tests\Unit\Managers;

use Workflowable\Workflow\Contracts\WorkflowActionContract;
use Workflowable\Workflow\Managers\WorkflowActionManager;
use Workflowable\Workflow\Tests\Fakes\WorkflowActionFake;
use Workflowable\Workflow\Tests\TestCase;

class WorkflowActionManagerTest extends TestCase
{
    protected WorkflowActionContract $dummyWorkflowAction;

    public function setUp(): void
    {
        parent::setUp();

        $this->dummyWorkflowAction = new WorkflowActionFake();
    }

    public function test_that_we_can_register_a_workflow_action(): void
    {
        $workflowActionManager = new WorkflowActionManager();
        $workflowActionManager->register($this->dummyWorkflowAction);

        $this->assertTrue($workflowActionManager->isRegistered('workflow_action_fake'));
    }

    public function test_that_we_can_get_a_workflow_action(): void
    {
        $workflowActionManager = new WorkflowActionManager();
        $workflowActionManager->register($this->dummyWorkflowAction);

        $workflowAction = $workflowActionManager->getImplementation('workflow_action_fake');
        $this->assertInstanceOf(WorkflowActionContract::class, $workflowAction);
        $this->assertEquals('Workflow Action Fake', $workflowAction->getFriendlyName());
    }

    public function test_that_we_can_get_the_rules_for_a_workflow_action(): void
    {
        $workflowActionManager = new WorkflowActionManager();
        $workflowActionManager->register($this->dummyWorkflowAction);

        $rules = $workflowActionManager->getRules($this->dummyWorkflowAction->getAlias());
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('test', $rules);
        $this->assertEquals('required', $rules['test']);
    }

    public function test_that_we_can_check_if_a_workflow_action_is_valid(): void
    {
        $workflowActionManager = new WorkflowActionManager();
        $workflowActionManager->register($this->dummyWorkflowAction);

        $this->assertTrue($workflowActionManager->isValid($this->dummyWorkflowAction->getAlias(), ['test' => 'test']));
        $this->assertFalse($workflowActionManager->isValid($this->dummyWorkflowAction->getAlias(), []));
    }

    public function test_that_we_can_check_if_a_workflow_action_is_registered(): void
    {
        $workflowActionManager = new WorkflowActionManager();
        $workflowActionManager->register($this->dummyWorkflowAction);

        $this->assertTrue($workflowActionManager->isRegistered($this->dummyWorkflowAction->getAlias()));
        $this->assertFalse($workflowActionManager->isRegistered('test_workflow_action_2'));
    }
}
