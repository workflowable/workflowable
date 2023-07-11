<?php

namespace Workflowable\Workflowable\Tests\Traits;

use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowRun;
use Workflowable\Workflowable\Models\WorkflowRunStatus;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowStep;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowStepTypeFake;

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
            ->withParameters()
            ->create();
        $this->toWorkflowStep = WorkflowStep::factory()
            ->withWorkflowStepType(new WorkflowStepTypeFake())
            ->withWorkflow($this->workflow)
            ->withParameters()
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
