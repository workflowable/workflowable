<?php

namespace Workflowable\Workflowable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowActivityTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeEventConstrainedFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowConditionTypeFake;
use Workflowable\Workflowable\Tests\Fakes\WorkflowEventFake;
use Workflowable\Workflowable\WorkflowableServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:refresh', ['--database' => 'testing']);
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

        config()->set('workflowable.workflow_activity_types', [
            WorkflowActivityTypeEventConstrainedFake::class,
            WorkflowActivityTypeFake::class,
        ]);

        config()->set('workflowable.workflow_condition_types', [
            WorkflowConditionTypeFake::class,
            WorkflowConditionTypeEventConstrainedFake::class,
        ]);

        config()->set('workflowable.workflow_events', [
            WorkflowEventFake::class,
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_core_table.php.stub';
        $migration->up();
        */
    }
}
