<?php

namespace Workflowable\Workflow\Tests\Unit\Managers;

use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Managers\WorkflowConditionTypeTypeManager;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class WorkflowConditionTypeManagerTest extends TestCase
{
    protected WorkflowConditionTypeContract $dummyWorkflowCondition;

    public function setUp(): void
    {
        parent::setUp();

        $this->dummyWorkflowCondition = new WorkflowConditionTypeFake();
    }

    public function test_that_we_can_register_a_workflow_condition(): void
    {
        $workflowConditionManager = new WorkflowConditionTypeTypeManager();
        $workflowConditionManager->register($this->dummyWorkflowCondition);

        $this->assertTrue($workflowConditionManager->isRegistered($this->dummyWorkflowCondition->getAlias()));

    }

    public function test_that_we_can_get_a_workflow_condition(): void
    {
        $workflowConditionManager = new WorkflowConditionTypeTypeManager();
        $workflowConditionManager->register($this->dummyWorkflowCondition);

        $workflowCondition = $workflowConditionManager->getImplementation($this->dummyWorkflowCondition->getAlias());
        $this->assertInstanceOf(WorkflowConditionTypeContract::class, $workflowCondition);
        $this->assertEquals('Workflow Condition Fake', $workflowCondition->getFriendlyName());
        $this->assertEquals('workflow_condition_fake', $workflowCondition->getAlias());
    }

    public function test_that_we_can_get_a_workflow_condition_rules(): void
    {
        $workflowConditionManager = new WorkflowConditionTypeTypeManager();
        $workflowConditionManager->register($this->dummyWorkflowCondition);

        $workflowCondition = $workflowConditionManager->getImplementation($this->dummyWorkflowCondition->getAlias());
        $this->assertEquals(['test' => 'required'], $workflowCondition->getRules());
    }

    public function test_that_we_can_check_if_a_workflow_condition_is_valid(): void
    {
        $workflowConditionManager = new WorkflowConditionTypeTypeManager();
        $workflowConditionManager->register($this->dummyWorkflowCondition);

        $isWorkflowConditionValid = $workflowConditionManager->isValidParameters($this->dummyWorkflowCondition->getAlias(), ['test' => 'test']);
        $this->assertTrue($isWorkflowConditionValid);

        $isWorkflowConditionValid = $workflowConditionManager->isValidParameters($this->dummyWorkflowCondition->getAlias(), []);
        $this->assertFalse($isWorkflowConditionValid);
    }

    public function test_that_we_can_check_if_a_workflow_condition_is_registered(): void
    {
        $workflowConditionManager = new WorkflowConditionTypeTypeManager();
        $workflowConditionManager->register($this->dummyWorkflowCondition);

        $this->assertTrue($workflowConditionManager->isRegistered($this->dummyWorkflowCondition->getAlias()));
        $this->assertFalse($workflowConditionManager->isRegistered('test_workflow_condition_2'));
    }
}
