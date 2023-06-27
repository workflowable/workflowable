<?php

namespace Workflowable\WorkflowEngine\Tests\Unit\Actions\WorkflowConditions;

use Workflowable\WorkflowEngine\Actions\WorkflowConditions\UpdateWorkflowConditionAction;
use Workflowable\WorkflowEngine\DataTransferObjects\WorkflowConditionData;
use Workflowable\WorkflowEngine\Models\Workflow;
use Workflowable\WorkflowEngine\Models\WorkflowCondition;
use Workflowable\WorkflowEngine\Models\WorkflowConditionType;
use Workflowable\WorkflowEngine\Models\WorkflowEngineParameter;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStatus;
use Workflowable\WorkflowEngine\Models\WorkflowStep;
use Workflowable\WorkflowEngine\Models\WorkflowTransition;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowEventFake;
use Workflowable\WorkflowEngine\Tests\Fakes\WorkflowStepTypeFake;
use Workflowable\WorkflowEngine\Tests\TestCase;

class UpdateWorkflowConditionActionTest extends TestCase
{
    public function test_that_we_can_update_a_workflow_condition_for_a_transition()
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

        $workflowCondition = WorkflowCondition::factory()
            ->withWorkflowTransition($workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        /** @var UpdateWorkflowConditionAction $action */
        $action = app(UpdateWorkflowConditionAction::class);

        $workflowConditionData = WorkflowConditionData::fromArray([
            'workflow_transition_id' => $workflowTransition->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
            'parameters' => [
                'test' => 'Bar',
            ],
            'ordinal' => 2,
        ]);

        $workflowCondition = $action->handle($workflowCondition, $workflowConditionData);

        $this->assertInstanceOf(WorkflowCondition::class, $workflowCondition);
        $this->assertDatabaseHas(WorkflowCondition::class, [
            'id' => $workflowCondition->id,
            'ordinal' => 2,
        ]);

        $this->assertDatabaseHas(WorkflowEngineParameter::class, [
            'parameterizable_type' => WorkflowCondition::class,
            'parameterizable_id' => $workflowCondition->id,
            'key' => 'test',
            'value' => 'Bar',
        ]);
    }
}
