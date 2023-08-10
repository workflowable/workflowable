<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowConditions;

use Workflowable\Workflowable\Actions\WorkflowConditions\UpdateWorkflowConditionAction;
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

class UpdateWorkflowConditionActionTest extends TestCase
{
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

        $this->assertDatabaseHas(WorkflowConditionParameter::class, [
            'workflow_condition_id' => $workflowCondition->id,
            'key' => 'test',
            'value' => 'Bar',
        ]);
    }
}
