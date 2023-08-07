<?php

namespace Workflowable\Workflowable\Tests\Traits;

use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessStatus;
use Workflowable\Workflowable\Models\WorkflowStatus;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;

trait HasWorkflowProcessTests
{
    protected Workflow $workflow;

    protected WorkflowProcess $workflowProcess;

    protected WorkflowEvent $workflowEvent;

    protected WorkflowActivity $fromWorkflowActivity;

    protected WorkflowActivity $toWorkflowActivity;

    protected WorkflowTransition $workflowTransition;

    public function setUp(): void
    {
        parent::setUp();

        $this->workflowEvent = WorkflowEvent::factory()->withContract(new WorkflowEventFake())->create();

        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatus::ACTIVE)
            ->create();

        $this->fromWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflow)
            ->withParameters()
            ->create();
        $this->toWorkflowActivity = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflow)
            ->withParameters()
            ->create();

        $this->workflowTransition = WorkflowTransition::factory()
            ->withWorkflow($this->workflow)
            ->withFromWorkflowActivity($this->fromWorkflowActivity)
            ->withToWorkflowActivity($this->toWorkflowActivity)
            ->create();

        $this->workflowProcess = WorkflowProcess::factory()
            ->withWorkflowProcessStatus(WorkflowProcessStatus::RUNNING)
            ->withWorkflow($this->workflow)
            ->withLastWorkflowActivity($this->fromWorkflowActivity)
            ->create();
    }
}
