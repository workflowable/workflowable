<?php

namespace Workflowable\Workflowable\Tests\Actions\Workflows;

use Workflowable\Workflowable\Actions\Workflows\CloneWorkflowAction;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\HasWorkflowProcess;
use Workflowable\Workflowable\Tests\TestCase;

class CloneWorkflowActionTest extends TestCase
{
    use HasWorkflowProcess;

    public function test_that_we_can_clone_a_workflow()
    {
        $workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

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

        $clonedWorkflow = CloneWorkflowAction::make()->handle($workflow, 'Cloned Workflow');

        $this->assertEquals('Cloned Workflow', $clonedWorkflow->name);

        collect([$fromWorkflowActivity, $toWorkflowActivity])->map(function ($workflowActivity) use ($clonedWorkflow) {
            $clonedActivityQuery = WorkflowActivity::query()
                ->where([
                    'workflow_id' => $clonedWorkflow->id,
                    'workflow_activity_type_id' => $workflowActivity->workflow_activity_type_id,
                    'name' => $workflowActivity->name,
                    'description' => $workflowActivity->description,
                ]);

            foreach ($workflowActivity->workflowActivityParameters as $parameter) {
                $clonedActivityQuery
                    ->whereHas('workflowActivityParameters', function ($query) use ($parameter) {
                        $query->where([
                            'key' => $parameter->key,
                            'value' => $parameter->value,
                        ]);
                    });
            }

            $result = $clonedActivityQuery->exists();

            $this->assertTrue($result);
        });

        $transitionWasCloned = WorkflowTransition::query()
            ->where('workflow_id', $clonedWorkflow->id)
            ->where('name', $workflowTransition->name)
            ->where('ordinal', $workflowTransition->ordinal)
            ->exists();

        $this->assertTrue($transitionWasCloned);
    }
}
