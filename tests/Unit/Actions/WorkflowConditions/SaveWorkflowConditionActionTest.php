<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowConditions;

use Workflowable\Workflowable\Actions\WorkflowConditions\SaveWorkflowConditionAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConditionParameter;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class SaveWorkflowConditionActionTest extends TestCase
{
    public function test_that_we_can_create_a_workflow_condition_for_a_transition()
    {
        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

        $workflowConditionData = WorkflowConditionData::fromArray([
            'workflow_transition_id' => $workflowTransition->id,
            'parameters' => [
                'test' => 'Test',
            ],
            'ordinal' => 1,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);

        $workflowCondition = SaveWorkflowConditionAction::make()->handle($workflowTransition, $workflowConditionData);

        $this->assertInstanceOf(WorkflowCondition::class, $workflowCondition);
        $this->assertDatabaseHas(WorkflowCondition::class, [
            'id' => $workflowCondition->id,
            'workflow_transition_id' => $workflowTransition->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);

        $this->assertDatabaseHas(WorkflowConditionParameter::class, [
            'workflow_condition_id' => $workflowCondition->id,
            'key' => 'test',
            'value' => 'Test',
        ]);
    }

    public function test_that_we_can_update_a_workflow_condition_for_a_transition()
    {
        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
        ]);

        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $workflowConditionType = WorkflowConditionType::factory()->withContract(new WorkflowConditionTypeFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::DRAFT)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

        $workflowCondition = WorkflowCondition::factory()
            ->withWorkflowTransition($workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        $workflowConditionData = WorkflowConditionData::fromArray([
            'workflow_transition_id' => $workflowTransition->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
            'parameters' => [
                'test' => 'Bar',
            ],
            'ordinal' => 2,
        ]);

        $workflowCondition = SaveWorkflowConditionAction::make()
            ->withWorkflowCondition($workflowCondition)
            ->handle($workflowTransition, $workflowConditionData);

        $this->assertInstanceOf(WorkflowCondition::class, $workflowCondition);
        $this->assertDatabaseHas(WorkflowCondition::class, [
            'id' => $workflowCondition->id,
            'ordinal' => 2,
        ]);

        $this->assertDatabaseHas(WorkflowConditionParameter::class, [
            'workflow_condition_id' => $workflowCondition->id,
            'key' => 'test',
            'value' => 'Bar',
        ]);
    }

    public function test_that_we_catch_invalid_workflow_condition_parameters()
    {
        $this->markTestIncomplete('Not written yet');
    }
}