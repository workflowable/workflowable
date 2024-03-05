<?php

namespace Workflowable\Workflowable\Tests\Actions\WorkflowActivityTypes;

use Workflowable\Workflowable\Actions\WorkflowActivityTypes\RegisterWorkflowActivityTypeAction;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowActivityTypeWorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class RegisterWorkflowActivityTypeActionTest extends TestCase
{
    public function test_registering_a_workflow_activity_type()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $bogusWorkflowEvent = WorkflowEvent::factory()->create([
            'name' => 'Bogus Workflow Event',
            'class_name' => 'className',
        ]);

        $workflowActivityType = RegisterWorkflowActivityTypeAction::make()->handle(new WorkflowActivityTypeFake());

        $this->assertInstanceOf(WorkflowActivityType::class, $workflowActivityType);

        $this->assertDatabaseHas(WorkflowActivityType::class, [
            'name' => (new WorkflowActivityTypeFake())->getName(),
            'class_name' => WorkflowActivityTypeFake::class,
        ]);

        $this->assertDatabaseHas(WorkflowActivityTypeWorkflowEvent::class, [
            'workflow_event_id' => $workflowEvent->id,
            'workflow_activity_type_id' => $workflowActivityType->id,
        ]);

        $this->assertDatabaseHas(WorkflowActivityTypeWorkflowEvent::class, [
            'workflow_event_id' => $bogusWorkflowEvent->id,
            'workflow_activity_type_id' => $workflowActivityType->id,
        ]);
    }

    public function test_handling_restricted_workflow_activity_types()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();
        $bogusWorkflowEvent = WorkflowEvent::factory()->create([
            'name' => 'Bogus Workflow Event',
            'class_name' => 'className',
        ]);

        $workflowActivityType = RegisterWorkflowActivityTypeAction::make()->handle(new WorkflowActivityTypeEventConstrainedFake());

        $this->assertInstanceOf(WorkflowActivityType::class, $workflowActivityType);

        $this->assertDatabaseHas(WorkflowActivityType::class, [
            'name' => (new WorkflowActivityTypeEventConstrainedFake())->getName(),
            'class_name' => WorkflowActivityTypeEventConstrainedFake::class,
        ]);

        $this->assertDatabaseHas(WorkflowActivityTypeWorkflowEvent::class, [
            'workflow_event_id' => $workflowEvent->id,
            'workflow_activity_type_id' => $workflowActivityType->id,
        ]);

        $this->assertDatabaseMissing(WorkflowActivityTypeWorkflowEvent::class, [
            'workflow_event_id' => $bogusWorkflowEvent->id,
            'workflow_activity_type_id' => $workflowActivityType->id,
        ]);
    }
}
