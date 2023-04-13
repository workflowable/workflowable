<?php

namespace Workflowable\Workflow\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Contracts\WorkflowConditionTypeManagerContract;
use Workflowable\Workflow\Contracts\WorkflowEventManagerContract;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Contracts\WorkflowStepTypeManagerContract;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStepType;

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
        $this->handleSeedingWorkflowableStepTypes();
        $this->handleSeedingWorkflowableConditionTypes();
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

    public function handleSeedingWorkflowableStepTypes(): void
    {
        /** @var array<WorkflowStepTypeContract> $workflowStepTypeContracts */
        $workflowStepTypeContracts = app(WorkflowStepTypeManagerContract::class)->getImplementations();

        foreach ($workflowStepTypeContracts as $workflowStepTypeContract) {
            $workflowStepType = WorkflowStepType::query()
                ->firstOrCreate([
                    'alias' => $workflowStepTypeContract->getAlias(),
                ], [
                    'friendly_name' => $workflowStepTypeContract->getFriendlyName(),
                    'alias' => $workflowStepTypeContract->getAlias(),
                    // If it's for an event, tag it with the workflow_event_id
                    'workflow_event_id' => $workflowStepTypeContract->getWorkflowEventAlias() ? WorkflowEvent::query()
                        ->where('alias', $workflowStepTypeContract->getWorkflowEventAlias())
                        ->firstOrFail()
                        ->id
                        : null,
                ]);

            if ($workflowStepType->wasRecentlyCreated) {
                $this->info('Created new workflow action type: '.$workflowStepType->friendly_name);
            }
        }
    }

    public function handleSeedingWorkflowableConditionTypes(): void
    {
        /** @var array<WorkflowConditionTypeContract> $workflowConditionContracts */
        $workflowConditionContracts = app(WorkflowConditionTypeManagerContract::class)->getImplementations();

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
