<?php

namespace Workflowable\Workflowable\Actions\WorkflowConditionTypes;

use Workflowable\Workflowable\Abstracts\AbstractAction;
use Workflowable\Workflowable\Contracts\ShouldRestrictToWorkflowEvents;
use Workflowable\Workflowable\Contracts\WorkflowConditionTypeContract;
use Workflowable\Workflowable\Models\WorkflowConditionType;
use Workflowable\Workflowable\Models\WorkflowConditionTypeWorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowEvent;

class CacheWorkflowConditionTypeImplementationsAction extends AbstractAction
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
            foreach (config('workflowable.workflow_condition_types') ?? [] as $workflowConditionTypeContract) {
                /** @var WorkflowConditionTypeContract $workflowConditionTypeContract */
                $workflowConditionTypeContract = app($workflowConditionTypeContract);

                $workflowConditionType = WorkflowConditionType::query()
                    ->firstOrCreate([
                        'alias' => $workflowConditionTypeContract->getAlias(),
                    ], [
                        'name' => $workflowConditionTypeContract->getName(),
                        'alias' => $workflowConditionTypeContract->getAlias(),
                    ]);

                if ($workflowConditionTypeContract instanceof ShouldRestrictToWorkflowEvents) {
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
