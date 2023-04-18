<?php

namespace Workflowable\Workflow\Actions\WorkflowStepTypes;

use Workflowable\Workflow\Contracts\WorkflowStepTypeContract;
use Workflowable\Workflow\Models\WorkflowEvent;
use Workflowable\Workflow\Models\WorkflowStepType;

class CacheWorkflowStepTypeImplementationAction
{
    protected bool $shouldBustCache = false;

    public function shouldBustCache(): self
    {
        return $this;
    }

    public function handle(): array
    {
        $key = config('workflowable.cache_keys.workflow_step_types');

        if ($this->shouldBustCache) {
            cache()->forget($key);
        }

        return cache()->rememberForever(config('workflowable.cache_keys.workflow_step_types'), function () {
            $mappedContracts = [];
            foreach (config('workflowable.workflow_step_types') as $workflowStepTypeContract) {
                $workflowStepTypeContract = app($workflowStepTypeContract);

                if (! $this->canCreateWorkflowStepType($workflowStepTypeContract)) {
                    continue;
                }

                $workflowStepType = WorkflowStepType::query()
                    ->firstOrCreate([
                        'alias' => $workflowStepTypeContract->getAlias(),
                    ], [
                        'friendly_name' => $workflowStepTypeContract->getFriendlyName(),
                        'alias' => $workflowStepTypeContract->getAlias(),
                        // If it's for an event, tag it with the workflow_event_id
                        'workflow_event_id' => $workflowStepTypeContract->getWorkflowEventAlias()
                            ? WorkflowEvent::query()
                                ->where('alias', $workflowStepTypeContract->getWorkflowEventAlias())
                                ->firstOrFail()
                                ->id
                            : null,
                    ]);

                $mappedContracts[$workflowStepType->id] = $workflowStepTypeContract;
            }

            return $mappedContracts;
        });
    }

    protected function canCreateWorkflowStepType(WorkflowStepTypeContract $workflowStepTypeContract): bool
    {
        if (empty($workflowStepTypeContract->getWorkflowEventAlias())) {
            return true;
        }

        return WorkflowEvent::query()
            ->where('alias', $workflowStepTypeContract->getWorkflowEventAlias())
            ->exists();
    }
}
