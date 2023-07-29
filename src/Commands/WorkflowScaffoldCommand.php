<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\CacheWorkflowActivityTypeImplementationsAction;
use Workflowable\Workflowable\Actions\WorkflowConditionTypes\CacheWorkflowConditionTypeImplementationsAction;
use Workflowable\Workflowable\Actions\WorkflowEvents\CacheWorkflowEventImplementationsAction;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowEvent;

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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding workflow events, conditions and actions');
        $this->handleSeedingWorkflowableEvents();

        $this->handleSeedingWorkflowableActivityTypes();

        $this->handleSeedingWorkflowableConditionTypes();
        $this->info('Seeding complete');
    }

    public function handleSeedingWorkflowableEvents(): void
    {
        $this->info('Seeding workflow events');
        $startedAt = now();

        app(CacheWorkflowEventImplementationsAction::class)->shouldBustCache()->handle();

        WorkflowEvent::query()
            ->where('created_at', '>=', $startedAt)
            ->chunkById(50, function ($workflowEvents) {
                foreach ($workflowEvents as $workflowEvent) {
                    $this->info('Created new workflow event: '.$workflowEvent->name);
                }
            });

        $this->info('Completed seeding workflow events');
    }

    public function handleSeedingWorkflowableActivityTypes(): void
    {
        $this->info('Seeding workflow activities types');

        $startedAt = now();
        app(CacheWorkflowActivityTypeImplementationsAction::class)->shouldBustCache()->handle();

        WorkflowActivityType::query()
            ->where('created_at', '>=', $startedAt)
            ->chunkById(50, function ($workflowActivityTypes) {
                foreach ($workflowActivityTypes as $workflowActivityType) {
                    $this->info('Created new workflow activity type: '.$workflowActivityType->name);
                }
            });

        $this->info('Completed seeding workflow activity types');
    }

    public function handleSeedingWorkflowableConditionTypes(): void
    {
        $this->info('Seeding workflow condition types');

        $startedAt = now();
        app(CacheWorkflowConditionTypeImplementationsAction::class)->shouldBustCache()->handle();

        WorkflowConditionType::query()
            ->where('created_at', '>=', $startedAt)
            ->chunkById(50, function ($workflowConditionTypes) {
                foreach ($workflowConditionTypes as $workflowConditionType) {
                    $this->info('Created new workflow condition type: '.$workflowConditionType->name);
                }
            });

        $this->info('Completed seeding workflow condition types');
    }
}
