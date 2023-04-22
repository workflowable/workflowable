<?php

namespace Workflowable\Workflow\Tests\Unit\Traits;

use Workflowable\Workflow\Exceptions\WorkflowConditionException;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowCondition;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;
use Workflowable\Workflow\Traits\CreatesWorkflowConditions;

class CreatesWorkflowConditionsTest extends TestCase
{
    protected $classCreatingConditions;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);

        config()->set('workflowable.workflow_step_types', [
            WorkflowStepTypeFake::class,
        ]);

        $this->classCreatingConditions = new class
        {
            use CreatesWorkflowConditions;
        };
    }

    public function getWorkflowTransitionTestData(): WorkflowTransition
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $workflowSteps = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->count(2)
            ->create();

        // Create a mock workflow transition
        return WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($workflowSteps[0])
            ->withToWorkflowStep($workflowSteps[1])
            ->create([
                'ordinal' => 1,
            ]);
    }

    public function test_add_workflow_condition_method_adds_workflow_condition_to_array()
    {
        // Add a new workflow condition
        $this->classCreatingConditions->addWorkflowCondition('workflow_condition_fake', 1, ['param1' => 'value1', 'param2' => 'value2']);

        // Assert that the newly added workflow condition is in the array
        $this->assertArrayHasKey(0, $this->classCreatingConditions->getWorkflowConditions());
        $this->assertEqualsCanonicalizing([
            'type' => 'workflow_condition_fake',
            'parameters' => ['param1' => 'value1', 'param2' => 'value2'],
            'ordinal' => 1,
        ], $this->classCreatingConditions->getWorkflowConditions()[0]);
    }

    public function test_create_workflow_conditions_method_creates_workflow_conditions_for_given_transition()
    {
        $workflowTransition = $this->getWorkflowTransitionTestData();

        // Add some workflow conditions
        $this->classCreatingConditions
            ->addWorkflowCondition('workflow_condition_fake', 1, ['test' => 'Test']);

        // Call the createWorkflowConditions method
        $createdWorkflowConditions = $this->classCreatingConditions->createWorkflowConditions($workflowTransition);

        // Assert that the returned array is not empty
        $this->assertNotEmpty($createdWorkflowConditions);

        // Assert that each workflow condition was created for the given transition
        foreach ($createdWorkflowConditions as $condition) {
            $this->assertInstanceOf(WorkflowCondition::class, $condition);
            $this->assertSame($workflowTransition->id, $condition->workflowTransition->id);
        }
    }

    public function test_ensure_failure_on_failed_parameter_validation()
    {
        $workflowTransition = $this->getWorkflowTransitionTestData();

        // Add some workflow conditions
        $this->classCreatingConditions
            ->addWorkflowCondition('workflow_condition_fake', 1, ['fail' => 'fail']);

        $this->expectException(WorkflowConditionException::class);
        $this->expectExceptionMessage(WorkflowConditionException::workflowConditionParametersInvalid()->getMessage());
        $this->classCreatingConditions->createWorkflowConditions($workflowTransition);
    }
}
