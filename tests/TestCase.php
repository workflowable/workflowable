<?php

namespace Workflowable\Workflowable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
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

        config()->set('workflowable.queue', 'default');
    }
}
