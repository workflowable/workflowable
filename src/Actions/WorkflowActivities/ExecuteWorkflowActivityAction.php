<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivities;

use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Actions\WorkflowActivityTypes\GetWorkflowActivityTypeImplementationAction;
use Workflowable\Workflowable\Enums\WorkflowActivityAttemptStatusEnum;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityCompleted;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityFailed;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityStarted;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowActivityAttempt;
use Workflowable\Workflowable\Models\WorkflowProcess;

class ExecuteWorkflowActivityAction extends AbstractAction
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     * @throws \Workflowable\Workflowable\Exceptions\WorkflowActivityException
     */
    public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity): WorkflowActivityAttempt
    {
        $startedAt = now();
        try {
            return DB::transaction(function () use ($workflowProcess, $workflowActivity, $startedAt) {
                WorkflowActivityStarted::dispatch($workflowProcess, $workflowActivity);

                // Retrieve the workflow action implementation and execute it
                $workflowActivityTypeContract = GetWorkflowActivityTypeImplementationAction::make()
                    ->handle($workflowActivity->workflow_activity_type_id);

                $workflowActivityTypeContract->handle($workflowProcess, $workflowActivity);

                $workflowActivityAttempt = $workflowProcess->workflowActivityAttempts()->create([
                    'workflow_activity_id' => $workflowActivity->id,
                    'workflow_activity_attempt_status_id' => WorkflowActivityAttemptStatusEnum::SUCCESS,
                    'started_at' => $startedAt,
                    'completed_at' => now(),
                ]);

                // Update the workflow process with the new last workflow activity
                $workflowProcess->last_workflow_activity_id = $workflowActivity->id;
                $workflowProcess->save();

                WorkflowActivityCompleted::dispatch($workflowProcess, $workflowActivity);

                return $workflowActivityAttempt;
            });
        } catch (\Throwable $th) {
            $workflowProcess->workflowActivityAttempts()->create([
                'workflow_activity_id' => $workflowActivity->id,
                'workflow_activity_attempt_status_id' => WorkflowActivityAttemptStatusEnum::FAILURE,
                'started_at' => $startedAt,
                'completed_at' => now(),
            ]);

            WorkflowActivityFailed::dispatch($workflowProcess, $workflowActivity);

            throw $th;
        }
    }
}
