<?php

namespace Workflowable\Workflow\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflow\Contracts\WorkflowActionContract;
use Workflowable\Workflow\Contracts\WorkflowActionManagerContract;
use Workflowable\Workflow\Contracts\WorkflowConditionContract;
use Workflowable\Workflow\Contracts\WorkflowConditionManagerContract;
use Workflowable\Workflow\Contracts\WorkflowEventManagerContract;
use Workflowable\Workflow\Models\WorkflowActionType;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;

class WorkflowableScaffoldCommand extends Command
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding workflowable events, conditions and actions');
        $this->handleSeedingWorkflowableEvents();
        $this->handleSeedingWorkflowableActions();
        $this->handleSeedingWorkflowableConditions();
        $this->info('Seeding complete');
    }

    public function handleSeedingWorkflowableEvents(): void
    {
        $workflowEventContracts = app(WorkflowEventManagerContract::class)->getImplementations();

        foreach ($workflowEventContracts as $workflowEventContract) {
            $workflowEvent = WorkflowEvent::query()
                ->firstOrCreate([
                    'alias' => $workflowEventContract->getAlias(),
                ], [
                    'friendly_name' => $workflowEventContract->getFriendlyName(),
                    'alias' => $workflowEventContract->getAlias(),
                ]);

            if ($workflowEvent->wasRecentlyCreated) {
                $this->info('Created new workflow event: '.$workflowEvent->friendly_name);
            }
        }
    }

    public function handleSeedingWorkflowableActions(): void
    {
        /** @var array<WorkflowActionContract> $workflowActionContracts */
        $workflowActionContracts = app(WorkflowActionManagerContract::class)->getImplementations();

        foreach ($workflowActionContracts as $workflowActionsContract) {
            $workflowActionType = WorkflowActionType::query()
                ->firstOrCreate([
                    'alias' => $workflowActionsContract->getAlias(),
                ], [
                    'friendly_name' => $workflowActionsContract->getFriendlyName(),
                    'alias' => $workflowActionsContract->getAlias(),
                    // If it's for an event, tag it with the workflow_event_id
                    'workflow_event_id' => $workflowActionsContract->getWorkflowEventAlias() ? WorkflowEvent::query()
                        ->where('alias', $workflowActionsContract->getWorkflowEventAlias())
                        ->firstOrFail()
                        ->id
                        : null,
                ]);

            if ($workflowActionType->wasRecentlyCreated) {
                $this->info('Created new workflow action type: '.$workflowActionType->friendly_name);
            }
        }
    }

    public function handleSeedingWorkflowableConditions(): void
    {
        /** @var array<WorkflowConditionContract> $workflowConditionContracts */
        $workflowConditionContracts = app(WorkflowConditionManagerContract::class)->getImplementations();

        foreach ($workflowConditionContracts as $workflowConditionContract) {
            $workflowConditionType = WorkflowConditionType::query()
                ->firstOrCreate([
                    'alias' => $workflowConditionContract->getAlias(),
                ], [
                    'friendly_name' => $workflowConditionContract->getFriendlyName(),
                    'alias' => $workflowConditionContract->getAlias(),
                    // If it's for an event, tag it with the workflow_event_id
                    'workflow_event_id' => $workflowConditionContract->getWorkflowEventAlias() ? WorkflowEvent::query()
                        ->where('alias', $workflowConditionContract->getWorkflowEventAlias())
                        ->firstOrFail()
                        ->id
                        : null,
                ]);

            if ($workflowConditionType->wasRecentlyCreated) {
                $this->info('Created new workflow condition type: '.$workflowConditionType->friendly_name);
            }
        }
    }
}
