<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivities;

use Illuminate\Support\Facades\DB;
use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Enums\WorkflowProcessActivityLogStatusEnum;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityCompleted;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityFailed;
use Workflowable\Workflowable\Events\WorkflowActivities\WorkflowActivityStarted;
use Workflowable\Workflowable\Models\WorkflowActivity;
use Workflowable\Workflowable\Models\WorkflowProcess;
use Workflowable\Workflowable\Models\WorkflowProcessActivityLog;

class ExecuteWorkflowActivityAction extends AbstractAction
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     * @throws \Workflowable\Workflowable\Exceptions\WorkflowActivityException
     */
    public function handle(WorkflowProcess $workflowProcess, WorkflowActivity $workflowActivity): WorkflowProcessActivityLog
    {
        $startedAt = now();
        try {
            return DB::transaction(function () use ($workflowProcess, $workflowActivity, $startedAt) {
                WorkflowActivityStarted::dispatch($workflowProcess, $workflowActivity);

                // Retrieve the workflow action implementation and execute it
                $workflowActivityTypeContract = app($workflowActivity->workflowActivityType->class_name);

                $workflowActivityTypeContract->handle($workflowProcess, $workflowActivity);

                $workflowProcessActivityLog = $workflowProcess->workflowProcessActivityLogs()->create([
                    'workflow_activity_id' => $workflowActivity->id,
                    'workflow_process_activity_log_status_id' => WorkflowProcessActivityLogStatusEnum::SUCCESS,
                    'started_at' => $startedAt,
                    'completed_at' => now(),
                ]);

                // Update the workflow process with the new last workflow activity
                $workflowProcess->last_workflow_activity_id = $workflowActivity->id;
                $workflowProcess->save();

                WorkflowActivityCompleted::dispatch($workflowProcess, $workflowActivity);

                return $workflowProcessActivityLog;
            });
        } catch (\Throwable $th) {
            $workflowProcess->workflowProcessActivityLogs()->create([
                'workflow_activity_id' => $workflowActivity->id,
                'workflow_process_activity_log_status_id' => WorkflowProcessActivityLogStatusEnum::FAILURE,
                'started_at' => $startedAt,
                'completed_at' => now(),
            ]);

            WorkflowActivityFailed::dispatch($workflowProcess, $workflowActivity);

            throw $th;
        }
    }
}
