<?php

namespace Workflowable\Workflow;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Workflowable\Workflow\Commands\MakeWorkflowActionCommand;
use Workflowable\Workflow\Commands\MakeWorkflowConditionCommand;
use Workflowable\Workflow\Commands\MakeWorkflowEventCommand;
use Workflowable\Workflow\Contracts\WorkflowActionContract;
use Workflowable\Workflow\Contracts\WorkflowActionManagerContract;
use Workflowable\Workflow\Contracts\WorkflowConditionContract;
use Workflowable\Workflow\Contracts\WorkflowConditionManagerContract;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Contracts\WorkflowEventManagerContract;
use Workflowable\Workflow\Managers\WorkflowActionManager;
use Workflowable\Workflow\Managers\WorkflowConditionManager;
use Workflowable\Workflow\Managers\WorkflowEventManager;

class WorkflowableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/workflowable.php',
            'workflowable'
        );

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->handleRegisteringWorkflowEvents();
        $this->handleRegisteringWorkflowConditions();
        $this->handleRegisteringWorkflowActions();

        // Register any commands created by the package
        $this->commands([
            Commands\WorkflowableScaffoldCommand::class,
            MakeWorkflowEventCommand::class,
            MakeWorkflowActionCommand::class,
            MakeWorkflowConditionCommand::class,
        ]);
    }

    public function handleRegisteringWorkflowEvents(): void
    {
        // Register core events with the core event manager as a singleton
        $this->app->singleton(WorkflowEventManagerContract::class, function ($app) {
            $manager = new WorkflowEventManager();

            /** @var array<WorkflowEventContract> $workflowEventContracts */
            $workflowEventContracts = config('workflowable.workflow_events');
            foreach ($workflowEventContracts as $workflowEventContract) {
                $manager->register(new $workflowEventContract);
            }

            return $manager;
        });
    }

    public function handleRegisteringWorkflowActions(): void
    {
        // Register core actions with the core action manager as a singleton
        $this->app->singleton(WorkflowActionManagerContract::class, function ($app) {
            $manager = new WorkflowActionManager();

            /** @var array<WorkflowActionContract> $workflowActionContracts */
            $workflowActionContracts = config('workflowable.workflow_actions');
            foreach ($workflowActionContracts as $workflowAction) {
                $manager->register(new $workflowAction);
            }

            return $manager;
        });
    }

    public function handleRegisteringWorkflowConditions(): void
    {
        // Register core conditions with the core condition manager as a singleton
        $this->app->singleton(WorkflowConditionManagerContract::class, function ($app) {
            $manager = new WorkflowConditionManager();

            /** @var array<WorkflowConditionContract> $workflowConditionContracts */
            $workflowConditionContracts = config('workflowable.workflow_conditions');
            foreach ($workflowConditionContracts as $workflowCondition) {
                $manager->register(new $workflowCondition);
            }

            return $manager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
