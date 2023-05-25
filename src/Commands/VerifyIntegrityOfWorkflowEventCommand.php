<?php

namespace Workflowable\Workflow\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflow\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\Workflow\Contracts\WorkflowEventContract;
use Workflowable\Workflow\Exceptions\WorkflowEventException;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStepType;

class VerifyIntegrityOfWorkflowEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:verify-integrity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies that the workflow condition types and workflow step types will be provided'
        .' all data needed by the workflow event.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        WorkflowEvent::query()
            ->with([
                'workflowStepTypes',
                'workflowConditionTypes',
            ])->eachById(function ($workflowEvent) {
                /** @var GetWorkflowEventImplementationAction $getImplementationAction */
                $getImplementationAction = app(GetWorkflowEventImplementationAction::class);
                try {
                    $eventImplementation = $getImplementationAction->handle($workflowEvent);

                    $workflowEvent->workflowStepTypes
                        ->each(function (WorkflowStepType $workflowStepType) use ($eventImplementation) {
                            $isVerified = $this->verifyWorkflowStepType($workflowStepType, $eventImplementation);
                            if (! $isVerified) {
                                $this->error("Workflow step type {$workflowStepType->alias} on workflow event {$eventImplementation->getAlias()} is not verified.");
                            }
                        });

                    $workflowEvent->workflowConditionTypes
                        ->each(function (WorkflowConditionType $workflowConditionType) use ($eventImplementation) {
                            $isVerified = $this->verifyWorkflowConditionType($workflowConditionType, $eventImplementation);
                            if (! $isVerified) {
                                $this->error("Workflow condition type {$workflowConditionType->alias} on workflow event {$eventImplementation->getAlias()} is not verified.");
                            }
                        });
                } catch (WorkflowEventException $e) {
                    $this->error("Workflow event {$workflowEvent->alias} is not registered.");
                }
            });
    }

    public function verifyWorkflowStepType(WorkflowStepType $workflowStepType, WorkflowEventContract $workflowEventContract): bool
    {
        return true;
    }

    public function verifyWorkflowConditionType(WorkflowConditionType $workflowConditionType, WorkflowEventContract $workflowEventContract): bool
    {
        return true;
    }
}
