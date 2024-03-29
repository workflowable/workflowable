<?php

namespace Workflowable\Workflowable\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflowable\Contracts\ShouldRequireInputTokens;
use Workflowable\Workflowable\Contracts\WorkflowEventContract;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Models\WorkflowActivityType;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowEvent;

class VerifyIntegrityOfWorkflowEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflowable:verify-integrity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies that the workflow condition types and workflow activity types will be provided'
        .' all data needed by the workflow event.';

    protected bool $hadIntegrityCheckFailure = false;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        WorkflowEvent::query()
            ->with([
                'workflowActivityTypes',
                'workflowConditionTypes',
            ])->eachById(function ($workflowEvent) {
                try {
                    $eventImplementation = app($workflowEvent->class_name);

                    $workflowEvent->workflowActivityTypes
                        ->each(function (WorkflowActivityType $workflowActivityType) use ($eventImplementation) {
                            $isVerified = $this->verifyWorkflowActivityType($workflowActivityType, $eventImplementation);
                            if (! $isVerified) {
                                $this->hadIntegrityCheckFailure = true;
                                $this->error("Workflow activity type {$workflowActivityType->name} on workflow event {$eventImplementation->getName()} is not verified.");
                            }
                        });

                    $workflowEvent->workflowConditionTypes
                        ->each(function (WorkflowConditionType $workflowConditionType) use ($eventImplementation) {
                            $isVerified = $this->verifyWorkflowConditionType($workflowConditionType, $eventImplementation);
                            if (! $isVerified) {
                                $this->hadIntegrityCheckFailure = true;
                                $this->error("Workflow condition type {$workflowConditionType->name} on workflow event {$eventImplementation->getName()} is not verified.");
                            }
                        });
                } catch (WorkflowEventException $e) {
                    $this->hadIntegrityCheckFailure = true;
                    $this->error("Workflow event {$workflowEvent->name} is not registered.");
                }
            });

        return (int) $this->hadIntegrityCheckFailure;
    }

    public function verifyWorkflowActivityType(WorkflowActivityType $workflowActivityType, WorkflowEventContract $workflowEventContract): bool
    {
        $activityTypeImplementation = app($workflowActivityType->class_name);

        $requiredEventKeys = $activityTypeImplementation instanceof ShouldRequireInputTokens
            ? $activityTypeImplementation->getRequiredWorkflowEventTokenKeys()
            : [];

        return empty(array_diff_key(array_flip($requiredEventKeys), array_keys($workflowEventContract->getTokens())));
    }

    public function verifyWorkflowConditionType(WorkflowConditionType $workflowConditionType, WorkflowEventContract $workflowEventContract): bool
    {
        $workflowConditionTypeImplementation = app($workflowConditionType->class_name);

        $requiredEventKeys = $workflowConditionTypeImplementation instanceof ShouldRequireInputTokens
            ? $workflowConditionTypeImplementation->getRequiredWorkflowEventTokenKeys()
            : [];

        return empty(array_diff_key(array_flip($requiredEventKeys), array_keys($workflowEventContract->getTokens())));
    }
}
