<?php

namespace Workflowable\Workflow\Actions\WorkflowConditionTypes;

use Workflowable\Workflow\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflow\Models\WorkflowConditionType;
use Workflowable\Workflow\Models\WorkflowConditionTypeWorkflowEvent;
use Workflowable\Workflow\Models\WorkflowEvent;

class CacheWorkflowConditionTypeImplementationsAction
{
    protected bool $shouldBustCache = false;

    public function shouldBustCache(): self
    {
        $this->shouldBustCache = true;

        return $this;
    }

    public function handle(): array
    {
        $key = config('workflowable.cache_keys.workflow_condition_types');

        if ($this->shouldBustCache) {
            cache()->forget($key);
        }

        return cache()->rememberForever($key, function () {
            $mappedContracts = [];
            foreach (config('workflowable.workflow_condition_types') as $workflowConditionTypeContract) {
                /** @var WorkflowConditionTypeContract $workflowConditionTypeContract */
                $workflowConditionTypeContract = app($workflowConditionTypeContract);

                if (! $this->canCreateWorkflowConditionType($workflowConditionTypeContract)) {
                    continue;
                }

                $workflowConditionType = WorkflowConditionType::query()
                    ->firstOrCreate([
                        'alias' => $workflowConditionTypeContract->getAlias(),
                    ], [
                        'friendly_name' => $workflowConditionTypeContract->getFriendlyName(),
                        'alias' => $workflowConditionTypeContract->getAlias(),
                        // If it's for an event, tag it with the workflow_event_id
                    ]);

                if (!empty($workflowConditionTypeContract->getWorkflowEventAlias())) {
                    $workflowEventId = WorkflowEvent::query()
                        ->where('alias', $workflowConditionTypeContract->getWorkflowEventAlias())
                        ->firstOrFail()
                        ->id;

                    WorkflowConditionTypeWorkflowEvent::query()->firstOrCreate([
                        'workflow_condition_type_id' => $workflowConditionType->id,
                        'workflow_event_id' => $workflowEventId,
                    ], [
                        'workflow_condition_type_id' => $workflowConditionType->id,
                        'workflow_event_id' => $workflowEventId,
                    ]);
                }

                $mappedContracts[$workflowConditionType->id] = $workflowConditionTypeContract::class;
            }

            return $mappedContracts;
        });
    }

    protected function canCreateWorkflowConditionType(WorkflowConditionTypeContract $workflowConditionTypeContract): bool
    {
        if (empty($workflowConditionTypeContract->getWorkflowEventAlias())) {
            return true;
        }

        return WorkflowEvent::query()
            ->where('alias', $workflowConditionTypeContract->getWorkflowEventAlias())
            ->exists();
    }
}
