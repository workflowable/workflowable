<?php

namespace Workflowable\Workflowable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Workflowable\Workflowable\Commands\MakeWorkflowActivityTypeCommand;
use Workflowable\Workflowable\Commands\MakeWorkflowConditionTypeCommand;
use Workflowable\Workflowable\Commands\MakeWorkflowEventCommand;
use Workflowable\Workflowable\Commands\VerifyIntegrityOfWorkflowEventCommand;
use Workflowable\Workflowable\Commands\WorkflowScaffoldCommand;

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
            ->name('workflowable')
            ->hasCommands([
                WorkflowScaffoldCommand::class,
                MakeWorkflowEventCommand::class,
                MakeWorkflowActivityTypeCommand::class,
                MakeWorkflowConditionTypeCommand::class,
                VerifyIntegrityOfWorkflowEventCommand::class,
            ]);

        $this->publishes([
            __DIR__.'/../config/workflowable.php' => config_path('workflowable.php'),
        ], 'workflowable-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
