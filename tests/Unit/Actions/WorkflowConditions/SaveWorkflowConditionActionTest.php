<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowConditions;

use Workflowable\Workflowable\Actions\WorkflowConditions\SaveWorkflowConditionAbstractAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowConditionData;
use Workflowable\Workflowable\Exceptions\InvalidWorkflowParametersException;
use Workflowable\Workflowable\Models\WorkflowCondition;
use Workflowable\Workflowable\Models\WorkflowConditionParameter;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\TestCase;
use Workflowable\Workflowable\Tests\Traits\HasWorkflowProcess;

class SaveWorkflowConditionActionTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_create_a_workflow_condition_for_a_transition()
    {
        $workflowConditionType = WorkflowConditionType::query()
            ->where('class_name', WorkflowConditionTypeFake::class)
            ->firstOrFail();

        $workflowConditionData = WorkflowConditionData::fromArray([
            'workflow_transition_id' => $this->workflowTransition->id,
            'parameters' => [
                'test' => 'Test',
            ],
            'ordinal' => 1,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);

        $workflowCondition = SaveWorkflowConditionAbstractAction::make()->handle($this->workflowTransition, $workflowConditionData);

        $this->assertInstanceOf(WorkflowCondition::class, $workflowCondition);
        $this->assertDatabaseHas(WorkflowCondition::class, [
            'id' => $workflowCondition->id,
            'workflow_transition_id' => $this->workflowTransition->id,
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
        $workflowConditionType = WorkflowConditionType::query()
            ->where('class_name', WorkflowConditionTypeFake::class)
            ->firstOrFail();

        $workflowCondition = WorkflowCondition::factory()
            ->withWorkflowTransition($this->workflowTransition)
            ->withWorkflowConditionType($workflowConditionType)
            ->create();

        $workflowConditionData = WorkflowConditionData::fromArray([
            'workflow_transition_id' => $this->workflowTransition->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
            'parameters' => [
                'test' => 'Bar',
            ],
            'ordinal' => 2,
        ]);

        $workflowCondition = SaveWorkflowConditionAbstractAction::make()
            ->withWorkflowCondition($workflowCondition)
            ->handle($this->workflowTransition, $workflowConditionData);

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
        $workflowConditionType = WorkflowConditionType::query()
            ->where('class_name', WorkflowConditionTypeFake::class)
            ->firstOrFail();

        $workflowConditionData = WorkflowConditionData::fromArray([
            'workflow_transition_id' => $this->workflowTransition->id,
            'parameters' => [],
            'ordinal' => 1,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);

        $this->expectException(InvalidWorkflowParametersException::class);
        $this->expectExceptionMessage((new InvalidWorkflowParametersException)->getMessage());
        SaveWorkflowConditionAbstractAction::make()->handle($this->workflowTransition, $workflowConditionData);
    }
}
