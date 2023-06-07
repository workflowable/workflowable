<?php

namespace Workflowable\Workflow\Tests\Unit\Actions\WorkflowConditions;

use Workflowable\Workflow\Actions\WorkflowConditions\CreateWorkflowConditionAction;
use Workflowable\Workflow\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowCondition;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\Workflow\Tests\TestCase;

class CreateWorkflowConditionActionTest extends TestCase
{
    public function test_that_we_can_create_a_workflow_condition_for_a_transition()
    {
        config()->set('workflow-engine.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::DRAFT)
            ->create();

        $fromWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowStep($fromWorkflowStep)
            ->withToWorkflowStep($toWorkflowStep)
            ->create();

        /** @var CreateWorkflowConditionAction $action */
        $action = app(CreateWorkflowConditionAction::class);

        $workflowConditionData = WorkflowConditionData::fromArray([
            'workflow_transition_id' => $workflowTransition->id,
            'parameters' => [
                'test' => 'Test',
            ],
            'ordinal' => 1,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);

        $workflowCondition = $action->handle($workflowConditionData);

        $this->assertInstanceOf(WorkflowCondition::class, $workflowCondition);
        $this->assertDatabaseHas(WorkflowCondition::class, [
            'id' => $workflowCondition->id,
            'workflow_transition_id' => $workflowTransition->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
            'parameters->test' => 'Test',
        ]);
    }
}
