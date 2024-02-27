<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\WorkflowConditionTypes;

use Workflowable\Workflowable\Actions\WorkflowConditionTypes\RegisterWorkflowConditionTypeAction;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowConditionTypeWorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class RegisterWorkflowConditionTypeActionTest extends TestCase
{
    public function test_registering_a_workflow_condition_type()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $bogusWorkflowEvent = WorkflowEvent::factory()->create([
            'name' => 'Bogus Workflow Event',
            'class_name' => 'className',
        ]);

        $workflowConditionType = RegisterWorkflowConditionTypeAction::make()->handle(new WorkflowConditionTypeFake());

        $this->assertInstanceOf(WorkflowConditionType::class, $workflowConditionType);

        $this->assertDatabaseHas(WorkflowConditionType::class, [
            'name' => (new WorkflowConditionTypeFake())->getName(),
            'class_name' => WorkflowConditionTypeFake::class,
        ]);

        $this->assertDatabaseHas(WorkflowConditionTypeWorkflowEvent::class, [
            'workflow_event_id' => $workflowEvent->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);

        $this->assertDatabaseHas(WorkflowConditionTypeWorkflowEvent::class, [
            'workflow_event_id' => $bogusWorkflowEvent->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);
    }

    public function test_registering_a_constrained_workflow_condition_type()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $bogusWorkflowEvent = WorkflowEvent::factory()->create([
            'name' => 'Bogus Workflow Event',
            'class_name' => 'className',
        ]);

        $workflowConditionType = RegisterWorkflowConditionTypeAction::make()->handle(new WorkflowConditionTypeEventConstrainedFake());

        $this->assertInstanceOf(WorkflowConditionType::class, $workflowConditionType);

        $this->assertDatabaseHas(WorkflowConditionType::class, [
            'name' => (new WorkflowConditionTypeEventConstrainedFake())->getName(),
            'class_name' => WorkflowConditionTypeEventConstrainedFake::class,
        ]);

        $this->assertDatabaseHas(WorkflowConditionTypeWorkflowEvent::class, [
            'workflow_event_id' => $workflowEvent->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);

        $this->assertDatabaseMissing(WorkflowConditionTypeWorkflowEvent::class, [
            'workflow_event_id' => $bogusWorkflowEvent->id,
            'workflow_condition_type_id' => $workflowConditionType->id,
        ]);
    }
}
