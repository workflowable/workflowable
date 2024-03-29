<?php

namespace Workflowable\Workflowable\Tests;

use Workflowable\Workflowable\Actions\WorkflowActivityTypes\RegisterWorkflowActivityTypeAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\RegisterWorkflowConditionTypeAction;
use Workflowable\Workflowable\Actions\WorkflowEvents\RegisterWorkflowEventAction;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Enums\WorkflowSwapStatusEnum;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;
use Workflowable\Workflowable\Models\WorkflowSwap;
use Workflowable\Workflowable\Models\WorkflowSwapActivityMap;
use Workflowable\Workflowable\Models\WorkflowTransition;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;

trait HasWorkflowSwaps
{
    protected Workflow $workflowOne;

    protected Workflow $workflowTwo;

    protected WorkflowProcess $workflowProcess;

    protected WorkflowEvent $workflowEvent;

    protected WorkflowActivity $fromWorkflowActivityOne;

    protected WorkflowActivity $toWorkflowActivityOne;

    protected WorkflowActivity $fromWorkflowActivityTwo;

    protected WorkflowActivity $toWorkflowActivityTwo;

    protected WorkflowTransition $workflowTransitionOne;

    protected WorkflowTransition $workflowTransitionTwo;

    protected WorkflowSwap $workflowSwap;

    protected WorkflowSwapActivityMap $workflowSwapActivityMapOne;

    protected WorkflowSwapActivityMap $workflowSwapActivityMapTwo;

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

        $this->workflowOne = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $this->workflowTwo = Workflow::factory()
            ->withWorkflowEvent($this->workflowEvent)
            ->withWorkflowStatus(WorkflowStatusEnum::ACTIVE)
            ->create();

        $this->fromWorkflowActivityOne = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflowOne)
            ->withParameters()
            ->create();
        $this->toWorkflowActivityOne = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflowOne)
            ->withParameters()
            ->create();

        $this->fromWorkflowActivityTwo = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflowTwo)
            ->withParameters()
            ->create();
        $this->toWorkflowActivityTwo = WorkflowActivity::factory()
            ->withWorkflowActivityType(new WorkflowActivityTypeFake())
            ->withWorkflow($this->workflowTwo)
            ->withParameters()
            ->create();

        $this->workflowTransitionOne = WorkflowTransition::factory()
            ->withWorkflow($this->workflowOne)
            ->withFromWorkflowActivity($this->fromWorkflowActivityOne)
            ->withToWorkflowActivity($this->toWorkflowActivityOne)
            ->create();

        $this->workflowTransitionTwo = WorkflowTransition::factory()
            ->withWorkflow($this->workflowTwo)
            ->withFromWorkflowActivity($this->fromWorkflowActivityTwo)
            ->withToWorkflowActivity($this->toWorkflowActivityTwo)
            ->create();

        $this->workflowProcess = WorkflowProcess::factory()
            ->withWorkflowProcessStatus(WorkflowProcessStatusEnum::PENDING)
            ->withWorkflow($this->workflowOne)
            ->withLastWorkflowActivity($this->fromWorkflowActivityOne)
            ->create();

        WorkflowProcessToken::factory()
            ->withWorkflowProcess($this->workflowProcess)
            ->create([
                'key' => 'test',
                'value' => 'test',
            ]);

        $this->workflowSwap = WorkflowSwap::factory()
            ->withWorkflowSwapStatus(WorkflowSwapStatusEnum::Draft)
            ->withFromWorkflow($this->workflowOne)
            ->withToWorkflow($this->workflowTwo)
            ->create();

        $this->workflowSwapActivityMapOne = WorkflowSwapActivityMap::factory()
            ->withFromWorkflowActivity($this->fromWorkflowActivityOne)
            ->withToWorkflowActivity($this->toWorkflowActivityOne)
            ->withWorkflowSwap($this->workflowSwap)
            ->create();

        $this->workflowSwapActivityMapTwo = WorkflowSwapActivityMap::factory()
            ->withFromWorkflowActivity($this->fromWorkflowActivityTwo)
            ->withToWorkflowActivity($this->toWorkflowActivityTwo)
            ->withWorkflowSwap($this->workflowSwap)
            ->create();
    }
}
