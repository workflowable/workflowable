<?php

namespace Workflowable\Workflow;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Workflowable\Workflow\Commands\MakeWorkflowConditionTypeCommand;
use Workflowable\Workflow\Commands\MakeWorkflowEventCommand;
use Workflowable\Workflow\Commands\MakeWorkflowStepTypeCommand;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeManagerContract;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Contracts\WorkflowEventManagerContract;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Contracts\WorkflowStepTypeManagerContract;
use Workflowable\Workflow\Managers\WorkflowConditionTypeTypeManager;
use Workflowable\Workflow\Managers\WorkflowEventManager;
use Workflowable\Workflow\Managers\WorkflowStepTypeTypeManager;

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
        $this->handleRegisteringWorkflowConditionTypes();
        $this->handleRegisteringWorkflowStepTypes();

        // Register any commands created by the package
        $this->commands([
            Commands\WorkflowableScaffoldCommand::class,
            MakeWorkflowEventCommand::class,
            MakeWorkflowStepTypeCommand::class,
            MakeWorkflowConditionTypeCommand::class,
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

    public function handleRegisteringWorkflowStepTypes(): void
    {
        // Register core actions with the core action manager as a singleton
        $this->app->singleton(WorkflowStepTypeManagerContract::class, function ($app) {
            $manager = new WorkflowStepTypeTypeManager();

            /** @var array<WorkflowStepTypeContract> $workflowStepTypeContracts */
            $workflowStepTypeContracts = config('workflowable.workflow_step_types');
            foreach ($workflowStepTypeContracts as $workflowStepTypeContract) {
                $manager->register(new $workflowStepTypeContract);
            }

            return $manager;
        });
    }

    public function handleRegisteringWorkflowConditionTypes(): void
    {
        // Register core conditions with the core condition manager as a singleton
        $this->app->singleton(WorkflowConditionTypeManagerContract::class, function ($app) {
            $manager = new WorkflowConditionTypeTypeManager();

            /** @var array<WorkflowConditionTypeContract> $workflowConditionTypeContracts */
            $workflowConditionTypeContracts = config('workflowable.workflow_condition_types');
            foreach ($workflowConditionTypeContracts as $workflowConditionTypeContract) {
                $manager->register(new $workflowConditionTypeContract);
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
