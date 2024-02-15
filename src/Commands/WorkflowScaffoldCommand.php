<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowActivityType;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowConditionType;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\RegisterWorkflowActivityTypeAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\RegisterWorkflowConditionTypeAction;
use Workflowable\Workflowable\Actions\WorkflowEvents\RegisterWorkflowEventAction;
use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;

class WorkflowScaffoldCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflowable:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Can be used upon deploy to ensure that all workflow events, conditions and actions are registered.';

    private array $blacklistedClasses = [
        AbstractWorkflowEvent::class,
        AbstractWorkflowConditionType::class,
        AbstractWorkflowActivityType::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding workflow events, conditions and activities');

        $declaredClasses = collect(get_declared_classes())->reject(function($declaredClass) {
            /**
             * Filter out black listed classes like abstract classes which might implement the interface,
             * but cannot be initialized
             */
            if (in_array($declaredClass, $this->blacklistedClasses)) {
                return true;
            }

            // Filter out mocks so it doesn't break tests
            if (Str::startsWith($declaredClass, 'Mockery')) {
                return true;
            }

            return false;
        });

        // Build out workflow events first, since activity and condition types depend on it
        foreach ($declaredClasses as $declaredClass) {
            if (in_array(WorkflowEventContract::class, class_implements($declaredClass))) {
                $workflowEvent = RegisterWorkflowEventAction::make()->handle(new $declaredClass);

                if ($workflowEvent->wasRecentlyCreated) {
                    $this->info('Created new workflow event: '.$workflowEvent->name);
                }
            }
        }

        // Handle registering conditions and activity types
        foreach ($declaredClasses as $declaredClass) {
            if (in_array(WorkflowConditionTypeContract::class, class_implements($declaredClass))) {
                $workflowConditionType = RegisterWorkflowConditionTypeAction::make()->handle(new $declaredClass);

                if ($workflowConditionType->wasRecentlyCreated) {
                    $this->info('Created new workflow condition type: '.$workflowConditionType->name);
                }
            } elseif (in_array(WorkflowActivityTypeContract::class, class_implements($declaredClass))) {
                $workflowActivityType = RegisterWorkflowActivityTypeAction::make()->handle(new $declaredClass);
                if ($workflowActivityType->wasRecentlyCreated) {
                    $this->info('Created new workflow activity type: '.$workflowActivityType->name);
                }
            }
        }

        $this->info('Seeding complete');
    }
}
