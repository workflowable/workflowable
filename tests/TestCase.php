<?php

namespace Workflowable\Workflowable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\RegisterWorkflowActivityTypeAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\RegisterWorkflowConditionTypeAction;
use Workflowable\Workflowable\Actions\WorkflowEvents\RegisterWorkflowEventAction;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\WorkflowableServiceProvider;

class TestCase extends Orchestra
{
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:refresh', ['--database' => 'testing']);

        foreach ($this->workflowEvents as $workflowEvent) {
            RegisterWorkflowEventAction::make()->handle(new $workflowEvent);
        }

        foreach ($this->workflowActivityTypes as $workflowActivity) {
            RegisterWorkflowActivityTypeAction::make()->handle(new $workflowActivity);
        }

        foreach ($this->workflowConditionTypes as $workflowConditionType) {
            RegisterWorkflowConditionTypeAction::make()->handle(new $workflowConditionType);
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            WorkflowableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        config()->set('workflowable.queue', 'default');
    }
}
