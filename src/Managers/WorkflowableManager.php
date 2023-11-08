<?php

namespace Workflowable\Workflowable\Managers;

use Illuminate\Support\Collection;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Concerns\InteractsWithWorkflowProcesses;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCreated;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessDispatched;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Jobs\WorkflowProcessRunnerJob;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessToken;

class WorkflowableManager
{
    /**
     * Takes a workflow event and triggers all workflows that are active and have a workflow event that matches the
     * event that was triggered
     */
    public function triggerEvent(AbstractWorkflowEvent $workflowEvent): Collection
    {
        // track the workflow runs that we are going to be dispatching
        $workflowProcessCollection = collect();

        /**
         * Find all workflows that are active and have a workflow event that matches the event that was triggered
         */
        Workflow::query()
            ->active()
            ->forEvent($workflowEvent)
            ->each(function (Workflow $workflow) use (&$workflowProcessCollection, $workflowEvent) {
                // Create the run
                $workflowProcess = $this->createWorkflowProcess($workflow, $workflowEvent);

                // Dispatch the run so that it can be processed
                $this->dispatchProcess($workflowProcess, $workflowEvent->getQueue());

                // Identify that the workflow run was spawned by the triggering of the event
                $workflowProcessCollection->push($workflowProcess);
            });

        // Return all the workflow runs that were spawned by the triggering of the event
        return $workflowProcessCollection;
    }

    /**
     * Creates a workflow process for the given workflow and workflow event
     *
     * @throws WorkflowEventException
     */
    public function createWorkflowProcess(Workflow $workflow, AbstractWorkflowEvent $workflowEvent): WorkflowProcess
    {
        $isValid = $workflowEvent->hasValidTokens();

        if (! $isValid) {
            throw WorkflowEventException::invalidWorkflowEventParameters();
        }

        // Create the workflow run and identify it as having been created
        $workflowProcess = new WorkflowProcess();
        $workflowProcess->workflow()->associate($workflow);
        $workflowProcess->workflowProcessStatus()->associate(WorkflowProcessStatusEnum::CREATED->value);
        $workflowProcess->save();

        // Create the workflow run parameters
        foreach ($workflowEvent->getTokens() as $key => $value) {
            $this->createInputToken($workflowProcess, $key, $value);
        }

        // Alert the system of the creation of a workflow run being created
        WorkflowProcessCreated::dispatch($workflowProcess);

        return $workflowProcess;
    }

    /**
     * Dispatches a workflow process so that it can be picked up by the workflow process runner
     */
    public function dispatchProcess(WorkflowProcess $workflowProcess, string $queue = 'default'): WorkflowProcess
    {
        // Identify the workflow run as being dispatched
        $workflowProcess->workflow_process_status_id = WorkflowProcessStatusEnum::DISPATCHED;
        $workflowProcess->save();

        // Dispatch the workflow run
        WorkflowProcessRunnerJob::dispatch($workflowProcess)->onQueue($queue);
        WorkflowProcessDispatched::dispatch($workflowProcess);

        return $workflowProcess;
    }

    /**
     * Creates an input parameter for the workflow process
     */
    public function createInputToken(WorkflowProcess $workflowProcess, string $key, mixed $value): WorkflowProcessToken
    {
        /** @var WorkflowProcessToken $workflowProcessToken */
        $workflowProcessToken = $workflowProcess->workflowProcessTokens()->create([
            'workflow_activity_id' => null,
            'key' => $key,
            'value' => $value,
        ]);

        return $workflowProcessToken;
    }

    /**
     * Creates an output token for the workflow process and identifies the activity that created it
     */
    public function createOutputToken(WorkflowProcess $workflowRun, WorkflowActivity $workflowActivity, string $key, mixed $value): WorkflowProcessToken
    {
        /** @var WorkflowProcessToken $workflowRunToken */
        $workflowRunToken = $workflowRun->workflowProcessTokens()->create([
            'workflow_activity_id' => $workflowActivity->id,
            'key' => $key,
            'value' => $value,
        ]);

        return $workflowRunToken;
    }
}
