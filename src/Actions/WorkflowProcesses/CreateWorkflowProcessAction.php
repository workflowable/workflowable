<?php

namespace Workflowable\Workflowable\Actions\WorkflowProcesses;

use Illuminate\Support\Carbon;
use Illuminate\Support\Traits\Conditionable;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Abstracts\AbstractWorkflowEvent;
use Workflowable\Workflowable\Enums\WorkflowProcessStatusEnum;
use Workflowable\Workflowable\Events\WorkflowProcesses\WorkflowProcessCreated;
use Workflowable\Workflowable\Exceptions\WorkflowEventException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;

class CreateWorkflowProcessAction extends AbstractAction
{
    use Conditionable;

    protected ?WorkflowActivity $lastWorkflowActivity = null;

    protected ?Carbon $nextRunAt = null;

    public function withNextRunAt(Carbon $nextRunAt): self
    {
        $this->nextRunAt = $nextRunAt;

        return $this;
    }

    public function withLastWorkflowActivity(WorkflowActivity $workflowActivity): self
    {
        $this->lastWorkflowActivity = $workflowActivity;

        return $this;
    }

    /**
     * @throws WorkflowEventException
     */
    public function handle(Workflow $workflow, AbstractWorkflowEvent $workflowEvent): WorkflowProcess
    {
        $isValid = $workflowEvent->hasValidTokens();

        if (! $isValid) {
            throw WorkflowEventException::invalidWorkflowEventParameters();
        }

        // Create the workflow run and identify it as having been created
        $workflowProcess = new WorkflowProcess();
        $workflowProcess->workflow()->associate($workflow);
        $workflowProcess->workflowProcessStatus()->associate(WorkflowProcessStatusEnum::CREATED->value);

        $this->when($this->lastWorkflowActivity, function () use ($workflowProcess) {
            $workflowProcess->lastWorkflowActivity()->associate($this->lastWorkflowActivity);
        });

        $this->when($this->nextRunAt, function () use ($workflowProcess) {
            $workflowProcess->next_run_at = $this->nextRunAt;
        });

        $workflowProcess->save();

        // Create the workflow run parameters
        foreach ($workflowEvent->getTokens() as $key => $value) {
            $workflowProcess->workflowProcessTokens()->create([
                'workflow_activity_id' => null,
                'key' => $key,
                'value' => $value,
            ]);
        }

        // Alert the system of the creation of a workflow run being created
        WorkflowProcessCreated::dispatch($workflowProcess);

        return $workflowProcess;
    }
}
