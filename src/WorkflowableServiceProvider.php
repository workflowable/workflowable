<?php

namespace Workflowable\Workflow;

use Illuminate\Support\ServiceProvider;
use Workflowable\Workflow\Commands\MakeWorkflowConditionTypeCommand;
use Workflowable\Workflow\Commands\MakeWorkflowEventCommand;
use Workflowable\Workflow\Commands\MakeWorkflowStepTypeCommand;
use Workflowable\Workflow\Commands\WorkflowScaffoldCommand;

class WorkflowableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/workflow-engine.php',
            'workflowable'
        );

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register any commands created by the package
        $this->commands([
            WorkflowScaffoldCommand::class,
            MakeWorkflowEventCommand::class,
            MakeWorkflowStepTypeCommand::class,
            MakeWorkflowConditionTypeCommand::class,
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
