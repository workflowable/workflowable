<?php

namespace Workflowable\WorkflowEngine;

use Illuminate\Support\ServiceProvider;
use Workflowable\WorkflowEngine\Commands\MakeWorkflowConditionTypeCommand;
use Workflowable\WorkflowEngine\Commands\MakeWorkflowEventCommand;
use Workflowable\WorkflowEngine\Commands\MakeWorkflowStepTypeCommand;
use Workflowable\WorkflowEngine\Commands\VerifyIntegrityOfWorkflowEventCommand;
use Workflowable\WorkflowEngine\Commands\WorkflowScaffoldCommand;

class WorkflowEngineServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/workflow-engine.php',
            'workflowable'
        );

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register any commands created by the package
        $this->commands([
            WorkflowScaffoldCommand::class,
            MakeWorkflowEventCommand::class,
            MakeWorkflowStepTypeCommand::class,
            MakeWorkflowConditionTypeCommand::class,
            VerifyIntegrityOfWorkflowEventCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
