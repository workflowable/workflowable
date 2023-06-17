<?php

namespace Workflowable\Workflow\Tests\Traits;

use Workflowable\Workflow\Models\Workflow;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
use Workflowable\Workflow\Models\WorkflowStatus;
use Workflowable\Workflow\Models\WorkflowStep;
use Workflowable\Workflow\Models\WorkflowTransition;
use Workflowable\Workflow\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflow\Tests\Fakes\WorkflowStepTypeFake;

trait HasWorkflowRunTests
{
    protected Workflow $workflow;

    protected WorkflowRun $workflowRun;

    protected WorkflowEvent $workflowEvent;

    protected WorkflowStep $fromWorkflowStep;

    protected WorkflowStep $toWorkflowStep;

    protected WorkflowTransition $workflowTransition;

    public function setUp(): void
    {
        parent::setUp();

        $this->workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $this->fromWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($this->workflow)
            ->create();
        $this->toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($this->workflow)
            ->create();

        $this->workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($this->workflow)
            ->withFromWorkflowStep($this->fromWorkflowStep)
            ->withToWorkflowStep($this->toWorkflowStep)
            ->create();

        $this->workflowRun = WorkflowRun::factory()
            ->withWorkflowRunStatus(WorkflowRunStatus::RUNNING)
            ->withWorkflow($this->workflow)
            ->withLastWorkflowStep($this->fromWorkflowStep)
            ->create();
    }
}
