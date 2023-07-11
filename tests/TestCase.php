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

        /*
        $migration = include __DIR__.'/../database/migrations/create_core_table.php.stub';
        $migration->up();
        */
    }
}
