<?php

namespace Workflowable\Workflowable\Actions\WorkflowActivityTypes;

use Workflowable\Workflowable\Contracts\WorkflowActivityTypeContract;
use Workflowable\Workflowable\Models\WorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowActivityTypeWorkflowEvent;
use Workflowable\Workflowable\Models\WorkflowActivityType;

class CacheWorkflowActivityTypeImplementationsAction
{
    protected bool $shouldBustCache = false;

    public function shouldBustCache(): self
    {
        $this->shouldBustCache = true;

        return $this;
    }

    public function handle(): array
    {
        $key = config('workflowable.cache_keys.workflow_activity_types');

        if ($this->shouldBustCache) {
            cache()->forget($key);
        }

        return cache()->rememberForever($key, function () {
            $mappedContracts = [];
            foreach (config('workflowable.workflow_activity_types') ?? [] as $workflowActivityTypeContract) {
                /** @var WorkflowActivityTypeContract $workflowActivityTypeContract */
                $workflowActivityTypeContract = app($workflowActivityTypeContract);

                $workflowActivityType = WorkflowActivityType::query()
                    ->firstOrCreate([
                        'alias' => $workflowActivityTypeContract->getAlias(),
                    ], [
                        'name' => $workflowActivityTypeContract->getName(),
                        'alias' => $workflowActivityTypeContract->getAlias(),
                    ]);

                if (! empty($workflowActivityTypeContract->getWorkflowEventAliases())) {
                    foreach ($workflowActivityTypeContract->getWorkflowEventAliases() as $workflowEventAlias) {
                        $workflowEventId = WorkflowEvent::query()
                            ->where('alias', $workflowEventAlias)
                            ->firstOrFail()
                            ->id;

                        WorkflowActivityTypeWorkflowEvent::query()->firstOrCreate([
                            'workflow_activity_type_id' => $workflowActivityType->id,
                            'workflow_event_id' => $workflowEventId,
                        ], [
                            'workflow_activity_type_id' => $workflowActivityType->id,
                            'workflow_event_id' => $workflowEventId,
                        ]);
                    }
                }

                $mappedContracts[$workflowActivityType->id] = $workflowActivityTypeContract::class;
            }

            return $mappedContracts;
        });
    }
}
