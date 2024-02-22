<?php

namespace Workflowable\Workflowable\Tests\Traits;

use Workflowable\Workflowable\Actions\WorkflowActivityTypes\RegisterWorkflowActivityTypeAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\RegisterWorkflowConditionTypeAction;
use Workflowable\Workflowable\Actions\WorkflowEvents\RegisterWorkflowEventAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;

trait HasWorkflowProcess
{
    protected Workflow $workflow;

    protected WorkflowProcess $workflowProcess;

    protected WorkflowEvent $workflowEvent;

    protected WorkflowActivity $fromWorkflowActivity;

    protected WorkflowActivity $toWorkflowActivity;

    protected WorkflowTransition $workflowTransition;

    protected array $workflowEvents = [
        WorkflowEventFake::class,
    ];

    protected array $workflowActivityTypes = [
        WorkflowActivityTypeFake::class,
        WorkflowActivityTypeEventConstrainedFake::class,
    ];

    protected array $workflowConditionTypes = [
        WorkflowConditionTypeEventConstrainedFake::class,
        WorkflowConditionTypeFake::class,
    ];

    public function setUp(): void
    {
        parent::setUp();

        foreach ($this->workflowEvents as $workflowEvent) {
            RegisterWorkflowEventAction::make()->handle(new $workflowEvent);
        }

        foreach ($this->workflowActivityTypes as $workflowActivity) {
            RegisterWorkflowActivityTypeAction::make()->handle(new $workflowActivity);
        }

        foreach ($this->workflowConditionTypes as $workflowConditionType) {
            RegisterWorkflowConditionTypeAction::make()->handle(new $workflowConditionType);
        }

        $this->workflowEvent = WorkflowEvent::query()->where('class_name', WorkflowEventFake::class)->firstOrFail();

        $this->workflow = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
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
            ->withWorkflowProcessStatus(WorkflowProcessStatusEnum::RUNNING)
            ->withWorkflow($this->workflow)
            ->withLastWorkflowActivity($this->fromWorkflowActivity)
            ->create();
    }
}
