<?php

namespace Workflowable\Workflowable\Tests\Unit\Actions\Workflows;

use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityParameter;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\TestCase;

class CloneWorkflowAction extends TestCase
{
    public function test_that_we_can_clone_a_workflow()
    {
        $workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $workflow = Workflow::factory()
            ->withWorkflowEvent($workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->withParameters()
            ->create();
        $toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($workflow)
            ->withParameters()
            ->create();

        $workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($workflow)
            ->withFromWorkflowActivity($fromWorkflowActivity)
            ->withToWorkflowActivity($toWorkflowActivity)
            ->create();

        $clonedWorkflow = $this->cloneWorkflow($workflow, 'Cloned Workflow');

        $this->assertEquals('Cloned Workflow', $clonedWorkflow->name);

        collect([$fromWorkflowActivity, $toWorkflowActivity])->map(function ($workflowActivity) use ($clonedWorkflow) {
            $this->assertDatabaseHas(WorkflowActivity::class, [
                'workflow_id' => $clonedWorkflow->id,
                'workflow_activity_type_id' => $workflowActivity->workflow_activity_type_id,
                'ux_uuid' => $workflowActivity->ux_uuid,
                'name' => $workflowActivity->name,
                'description' => $workflowActivity->description,
            ]);

            $clonedWorkflowActivity = WorkflowActivity::query()
                ->where('ux_uuid', $workflowActivity->ux_uuid)
                ->where('workflow_id', $clonedWorkflow->id)
                ->firstOrFail();

            foreach ($workflowActivity->workflowActivityParameters as $parameter) {
                $this->assertDatabaseHas(WorkflowActivityParameter::class, [
                    'workflow_activity_id' => $clonedWorkflowActivity->id,
                    'key' => $parameter->key,
                    'value' => $parameter->value,
                ]);
            }
        });

        $transitionWasCloned = WorkflowTransition::query()
            ->whereHas('fromWorkflowActivity', function ($query) use ($fromWorkflowActivity) {
                $query->where('ux_uuid', $fromWorkflowActivity->ux_uuid);
            })
            ->whereHas('toWorkflowActivity', function ($query) use ($toWorkflowActivity) {
                $query->where('ux_uuid', $toWorkflowActivity->ux_uuid);
            })
            ->where('workflow_id', $clonedWorkflow->id)
            ->where('name', $workflowTransition->name)
            ->where('ux_uuid', $workflowTransition->ux_uuid)
            ->where('ordinal', $workflowTransition->ordinal)
            ->exists();

        $this->assertTrue($transitionWasCloned);
    }
}
