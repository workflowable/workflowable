<?php

namespace Workflowable\WorkflowEngine\Commands;

use Illuminate\Console\Command;
use Workflowable\WorkflowEngine\Actions\WorkflowConditionTypes\GetWorkflowConditionTypeImplementationAction;
use Workflowable\WorkflowEngine\Actions\WorkflowEvents\GetWorkflowEventImplementationAction;
use Workflowable\WorkflowEngine\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\WorkflowEngine\Contracts\WorkflowEventContract;
use Workflowable\WorkflowEngine\Exceptions\WorkflowEventException;
use Workflowable\WorkflowEngine\Models\WorkflowConditionType;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowStepType;

class VerifyIntegrityOfWorkflowEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow-engine:verify-integrity';

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
        /** @var GetWorkflowStepTypeImplementationAction $getStepTypeImplementation */
        $getStepTypeImplementation = app(GetWorkflowStepTypeImplementationAction::class);
        $stepTypeImplementation = $getStepTypeImplementation->handle($workflowStepType);

        $requiredEventKeys = $stepTypeImplementation->getRequiredWorkflowEventParameterKeys();

        return empty(array_diff_key(array_flip($requiredEventKeys), $workflowEventContract->getRules()));
    }

    public function verifyWorkflowConditionType(WorkflowConditionType $workflowConditionType, WorkflowEventContract $workflowEventContract): bool
    {
        /** @var GetWorkflowConditionTypeImplementationAction $getConditionTypeAction */
        $getConditionTypeAction = app(GetWorkflowConditionTypeImplementationAction::class);
        $workflowConditionTypeImplementation = $getConditionTypeAction->handle($workflowConditionType);

        $requiredEventKeys = $workflowConditionTypeImplementation->getRequiredWorkflowEventParameterKeys();

        return empty(array_diff_key(array_flip($requiredEventKeys), $workflowEventContract->getRules()));
    }
}
