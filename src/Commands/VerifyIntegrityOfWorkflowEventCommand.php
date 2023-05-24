<?php

namespace Workflowable\Workflow\Commands;

use Illuminate\Console\Command;
use Workflowable\Workflow\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflow\Actions\WorkflowStepTypes\GetWorkflowStepTypeImplementationAction;
use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Events\WorkflowRuns\WorkflowRunDispatched;
use Workflowable\Workflow\Jobs\WorkflowRunnerJob;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowRun;
use Workflowable\Workflow\Models\WorkflowRunStatus;
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
        . ' all data needed by the workflow event.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $registeredWorkflowEvents = WorkflowEvent::query()
            ->with([
                'workflowStepTypes',
                'workflowConditionTypes',
            ])->get();

        $workflowEventImplementationClasses = config('workflowable.workflow_events');
        foreach ($workflowEventImplementationClasses as $workflowEventImplementationClass) {
            $implementation = new $workflowEventImplementationClass();

            $workflowEvent = $registeredWorkflowEvents->where('alias', $implementation->getAlias())->first();

            if (!$workflowEvent) {
                $this->error("Workflow event {$implementation->getAlias()} is not registered.");
                continue;
            }

            $workflowEvent->workflowStepTypes
                ->each(function (WorkflowStepType $workflowStepType) use ($workflowEvent) {
                    $isVerified = $this->verifyWorkflowStepType($workflowStepType, $workflowEvent);
                    if (!$isVerified) {
                        $this->error("Workflow step type {$workflowStepType->alias} on workflow event {$workflowEvent->alias} is not verified.");
                    }
                });

            $workflowEvent->workflowConditionTypes
                ->each(function (WorkflowConditionType $workflowConditionType) use ($workflowEvent) {
                    $isVerified = $this->verifyWorkflowConditionType($workflowConditionType, $workflowEvent);
                    if (!$isVerified) {
                        $this->error("Workflow condition type {$workflowConditionType->alias} on workflow event {$workflowEvent->alias} is not verified.");
                    }
                });
        }
    }

    public function verifyWorkflowStepType(WorkflowStepType $workflowStepType, AbstractWorkflowEvent $workflowEvent): bool
    {
        return true;
    }

    public function verifyWorkflowConditionType(WorkflowConditionType $workflowConditionType, WorkflowEvent $workflowEvent): bool
    {
        return true;
    }
}
