<?php

namespace Workflowable\Workflowable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Workflowable\Workflowable\Commands\WorkflowableCommand;

class WorkflowableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('core')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_core_table')
            ->hasCommand(WorkflowableCommand::class);
    }
}
