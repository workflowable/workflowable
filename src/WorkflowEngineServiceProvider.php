<?php

namespace Workflowable\WorkflowEngine;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Workflowable\WorkflowEngine\Commands\MakeWorkflowConditionTypeCommand;
use Workflowable\WorkflowEngine\Commands\MakeWorkflowEventCommand;
use Workflowable\WorkflowEngine\Commands\MakeWorkflowStepTypeCommand;
use Workflowable\WorkflowEngine\Commands\VerifyIntegrityOfWorkflowEventCommand;
use Workflowable\WorkflowEngine\Commands\WorkflowScaffoldCommand;

class WorkflowEngineServiceProvider  extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('workflow-engine')
            ->hasCommands([
                WorkflowScaffoldCommand::class,
                MakeWorkflowEventCommand::class,
                MakeWorkflowStepTypeCommand::class,
                MakeWorkflowConditionTypeCommand::class,
                VerifyIntegrityOfWorkflowEventCommand::class,
            ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
