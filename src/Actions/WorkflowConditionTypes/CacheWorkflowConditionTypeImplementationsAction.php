<?php

namespace Workflowable\WorkflowEngine\Actions\WorkflowConditionTypes;

use Workflowable\WorkflowEngine\Contracts\WorkflowConditionTypeContract;
use Workflowable\WorkflowEngine\Models\WorkflowConditionType;
use Workflowable\WorkflowEngine\Models\WorkflowConditionTypeWorkflowEvent;
use Workflowable\WorkflowEngine\Models\WorkflowEvent;

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
        $key = config('workflow-engine.cache_keys.workflow_condition_types');

        if ($this->shouldBustCache) {
            cache()->forget($key);
        }

        return cache()->rememberForever($key, function () {
            $mappedContracts = [];
            foreach (config('workflow-engine.workflow_condition_types') ?? [] as $workflowConditionTypeContract) {
                /** @var WorkflowConditionTypeContract $workflowConditionTypeContract */
                $workflowConditionTypeContract = app($workflowConditionTypeContract);

                $workflowConditionType = WorkflowConditionType::query()
                    ->firstOrCreate([
                        'alias' => $workflowConditionTypeContract->getAlias(),
                    ], [
                        'name' => $workflowConditionTypeContract->getName(),
                        'alias' => $workflowConditionTypeContract->getAlias(),
                    ]);

                if (! empty($workflowConditionTypeContract->getWorkflowEventAliases())) {
                    foreach ($workflowConditionTypeContract->getWorkflowEventAliases() as $workflowEventAlias) {
                        $workflowEventId = WorkflowEvent::query()
                            ->where('alias', $workflowEventAlias)
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
                }

                $mappedContracts[$workflowConditionType->id] = $workflowConditionTypeContract::class;
            }

            return $mappedContracts;
        });
    }
}
