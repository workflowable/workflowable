<?php

namespace Workflowable\Workflow\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflow\Actions\WorkflowConditionTypes\CacheWorkflowConditionTypeImplementationsAction;
use Workflowable\Workflow\Actions\WorkflowEvents\CacheWorkflowEventImplementationsAction;
use Workflowable\Workflow\Actions\WorkflowStepTypes\CacheWorkflowStepTypeImplementationsAction;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStepType;

class WorkflowScaffoldCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding workflowable events, conditions and actions');
        $this->handleSeedingWorkflowableEvents();

        $this->handleSeedingWorkflowableStepTypes();

        $this->handleSeedingWorkflowableConditionTypes();
        $this->info('Seeding complete');
    }

    public function handleSeedingWorkflowableEvents(): void
    {
        $this->info('Seeding workflowable events');
        $startedAt = now();

        app(CacheWorkflowEventImplementationsAction::class)->shouldBustCache()->handle();

        WorkflowEvent::query()
            ->where('created_at', '>=', $startedAt)
            ->chunkById(50, function ($workflowEvents) {
                foreach ($workflowEvents as $workflowEvent) {
                    $this->info('Created new workflow event: '.$workflowEvent->name);
                }
            });

        $this->info('Completed seeding workflowable events');
    }

    public function handleSeedingWorkflowableStepTypes(): void
    {
        $this->info('Seeding workflowable step types');

        $startedAt = now();
        app(CacheWorkflowStepTypeImplementationsAction::class)->shouldBustCache()->handle();

        WorkflowStepType::query()
            ->where('created_at', '>=', $startedAt)
            ->chunkById(50, function ($workflowStepTypes) {
                foreach ($workflowStepTypes as $workflowStepType) {
                    $this->info('Created new workflow step type: '.$workflowStepType->name);
                }
            });

        $this->info('Completed seeding workflowable step types');
    }

    public function handleSeedingWorkflowableConditionTypes(): void
    {
        $this->info('Seeding workflowable condition types');

        $startedAt = now();
        app(CacheWorkflowConditionTypeImplementationsAction::class)->shouldBustCache()->handle();

        WorkflowConditionType::query()
            ->where('created_at', '>=', $startedAt)
            ->chunkById(50, function ($workflowConditionTypes) {
                foreach ($workflowConditionTypes as $workflowConditionType) {
                    $this->info('Created new workflow condition type: '.$workflowConditionType->name);
                }
            });

        $this->info('Completed seeding workflowable condition types');
    }
}
