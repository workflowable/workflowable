<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivities;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\DataTransferObjects\WorkflowActivityData;
use Workflowable\Workflowable\Enums\WorkflowStatusEnum;
use Workflowable\Workflowable\Exceptions\InvalidWorkflowParametersException;
use Workflowable\Workflowable\Exceptions\WorkflowException;
use Workflowable\Workflowable\Models\Workflow;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityType;

class SaveWorkflowActivityAction extends AbstractAction
{
    protected WorkflowActivity $workflowActivity;

    public function __construct()
    {
        $this->workflowActivity = new WorkflowActivity();
    }

    public function withWorkflowActivity(WorkflowActivity $workflowActivity): self
    {
        $this->workflowActivity = $workflowActivity;

        return $this;
    }

    public function handle(Workflow|int $workflow, WorkflowActivityData $workflowActivityData): WorkflowActivity
    {
        if ($workflow->workflow_status_id !== WorkflowStatusEnum::DRAFT) {
            throw WorkflowException::workflowNotEditable();
        }

        $workflowActivityType = WorkflowActivityType::query()->findOrFail($workflowActivityData->workflow_activity_type_id);
        $workflowActivityTypeContract = app($workflowActivityType->class_name);

        $form = $workflowActivityTypeContract->makeForm()->fill($workflowActivityData->parameters);

        if (! $form->isValid()) {
            throw new InvalidWorkflowParametersException();
        }

        $this->workflowActivity->fill([
            'workflow_id' => $workflow instanceof Workflow
                ? $workflow->id
                : $workflow,
            'workflow_activity_type_id' => $workflowActivityData->workflow_activity_type_id,
            'name' => $workflowActivityData->name ?? 'N/A',
            'description' => $workflowActivityData->description ?? null,
        ]);

        $this->workflowActivity->save();

        if (! $this->workflowActivity->wasRecentlyCreated) {
            $this->workflowActivity->workflowActivityParameters()->delete();
        }

        // Create the workflow process parameters
        foreach ($form->getValues() as $name => $value) {
            $this->workflowActivity->workflowActivityParameters()->create([
                'key' => $name,
                'value' => $value,
            ]);
        }

        return $this->workflowActivity;
    }
}
